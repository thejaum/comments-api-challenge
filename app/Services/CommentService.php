<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use App\Repository\CommentRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Repository\PostRepositoryInterface;
use App\Repository\ApiSettingsRepositoryInterface;
use App\Exceptions\CustomValidationException;
use App\Models\Comment;
use App\Repository\HighlightCommentRepositoryInterface;
use App\Repository\TransactionRepositoryInterface;
use DateTime;
use Exception;
use Illuminate\Support\Facades\DB;

class CommentService
{
    private $repo_comment;
    private $repo_user;
    private $repo_post;
    private $repo_highlight;
    private $repo_transaction;
    private $repo_api_settings;
    private $service_notification;

    public function __construct(
        CommentRepositoryInterface $repo_comment
        ,UserRepositoryInterface $repo_user
        ,PostRepositoryInterface $repo_post
        ,ApiSettingsRepositoryInterface $repo_api_settings
        ,HighlightCommentRepositoryInterface $repo_highlight
        ,TransactionRepositoryInterface $repo_transaction
        ,NotificationService $service_notification)
    {
        $this->repo_comment = $repo_comment;
        $this->repo_user = $repo_user;
        $this->repo_post = $repo_post;
        $this->repo_api_settings = $repo_api_settings;
        $this->repo_highlight = $repo_highlight;
        $this->repo_transaction = $repo_transaction;
        $this->service_notification = $service_notification;
    }
 
    public function getAll($id_user,$id_post,$currentPage)
    {
        
        $cache_key = 'listcommets-'.$id_post.'-'.$currentPage;
        $allKeys = app('redis')->keys('listcommets-'.$id_post.'*');
        if(app('redis')->exists($cache_key))
            return unserialize(app('redis')->get($cache_key));
        
        $all_comments = $this->repo_comment->getAll($id_user,$id_post);
        /*
        * After retrieve all comments, we check if there's any highlithed and NOT expired comment.
        * separating in two arrays, of highlithed and not highlithed comments.
        */
        $date_now = date("Y-m-d H:i:s");
        $highlighted_comment=[];
        $normal_comment=[];
        foreach($all_comments->get() as $key => $value) {
            if ($value['expiration_date'] != null 
            && $date_now < $value['expiration_date']) {
                $highlighted_comment[] = $value;
            } else {
                $normal_comment[] = $value;
            }
        }
        if(count($highlighted_comment) > 0){
            /*
            * For the highlithed array, we sort now by coin_paid, because wo paid more must be
            * up than the others.
            */
            usort($highlighted_comment,function($first,$second){
                return $first->coin_paid < $second->coin_paid;
            });
            /*
            * With all sort flow done, we merge the arrays and create another array just with the
            * id_sequence. With that sequence we select comments again only for use paginate() of eloquent;
            */
            $array = array_merge($highlighted_comment, $normal_comment);
            $ids = []; 
            foreach ($array as &$value) {
                $ids[] = $value['id_comment'] ;
            };
            $list_comments = $this->repo_comment->getCommentsByArrayOfId($ids)->paginate();
            if($id_post != null)
                app('redis')->set($cache_key,serialize($list_comments));
            return $list_comments;
        }
        if($id_post != null && !$all_comments->get()->isEmpty())
            app('redis')->set($cache_key,serialize($all_comments->paginate()));
        return $all_comments->paginate();
    }

    public function get($id)
    {
        return $this->repo_comment->get($id);
    }

    public function store(array $data)
    {
        $validator = Validator::make($data, [
            'message' => 'required',
            'id_user' => 'required',
            'id_post' => 'required'
        ]);
        if ($validator->fails())
            throw new CustomValidationException('Falha na validação dos dados', $validator->errors()->toArray(),400);
        DB::beginTransaction();
        try {
            $user_commenting = $this->repo_user->get($data['id_user']);
            if($user_commenting == null)    
                throw new CustomValidationException('The id_user are invalid.',[
                'id_user' => $data['id_user']
                ],400);
            $post_owner = $this->repo_post->get($data['id_post']);
            if($post_owner == null)    
                throw new CustomValidationException('The id_post are invalid.',[
                'id_post' => $data['id_post']
                ],400);
            $user_post_owner = $this->repo_user->get($post_owner->id_user);
            if($user_post_owner == null)    
                throw new CustomValidationException('The user owner of post was not found',[
                'id_post' => $data['id_post']
                ],500);
            $api_settings = $this->repo_api_settings->get();
            if($api_settings == null)    
                throw new CustomValidationException('Api Settings not configured.'
                ,[],500);
            /*
            *  Check if user already reach the max numbers of comments allowed in the last X seconds.
            *  Quantity of comments and seconds are defined in api_settings table.
            */
            $comments_past_seconds = $this->repo_comment->getAmountOfCommentsByIdUserInLastSeconds($user_commenting->id_user,$api_settings->comments_allow_seconds);
            if ($comments_past_seconds >= $api_settings->comments_allow_amount)
                throw new CustomValidationException('Too much comments in last seconds, try again later.',[
                'comments_allow_seconds' => $api_settings->comments_allow_seconds
                ,'comments_allow_amount' => $api_settings->comments_allow_amount
                ,'user_comments_number' => $comments_past_seconds
                ],403);
            /*
            *  Check if user have balance to purchase the amount of highligth are attempt to do.
            */
            if(array_key_exists("highlight_minutes", $data)){
                $highlight_amount = $data['highlight_minutes'];
                if($user_commenting->coin_balance >= $highlight_amount){
                    $created_comment = $this->repo_comment->store($data);
                    $created_comment_id = $created_comment->id_comment;
                    $expiration_date = new \DateTime();
                    $expiration_date->modify('+'.$highlight_amount.' minutes');
                    $created_highlight_id = $this->repo_highlight->store([
                        'id_comment'=>$created_comment_id,
                        'expiration_date'=>$expiration_date,
                        'coin_paid'=>$highlight_amount
                    ])->id;
                    $data['id_comment'] = $created_comment_id;
                    $retain_system_percent = ($api_settings->retain_percentage / 100) * $highlight_amount;
                    $retain_system_percent = $retain_system_percent < 1 ? 1 : $retain_system_percent;
                    $this->repo_transaction->store([
                        'type'=>'RETAIN_COIN_OF_SYSTEM',
                        'id_highlight_comment'=>$created_highlight_id,    
                        'coin_amount'=> $retain_system_percent
                    ]);
                    $post_owner_receive = $highlight_amount - $retain_system_percent; 
                    $this->repo_transaction->store([
                        'type'=>'CREDIT_COIN_POST_OWNER',
                        'id_highlight_comment'=>$created_highlight_id,    
                        'coin_amount'=> $post_owner_receive
                    ]);
                    $user_commenting->coin_balance -= $highlight_amount;
                    $user_commenting->save();
                    $user_post_owner->coin_balance += $post_owner_receive;
                    $user_post_owner->save();
                    $this->service_notification->store([
                        'title'=>'You have a new comment.',
                        'message'=>$user_commenting->name.' commented in your post.',
                        'id_user'=>$user_post_owner->id_user
                    ]);
                    DB::commit();
                    $this->clearPostCommentsCache($data['id_post']);
                    return $created_comment;
                }else{
                    throw new CustomValidationException('User coins are not enought to purchase the amount of highlight.',[
                        'user_balance_coin' => $user_commenting->coin_balance,
                        'highlight_amount' => $highlight_amount
                        ],403);
                }
                
            }
            /*
            *  Check if user commenting is subscribed
            */
            if($user_commenting->subscribe){
                $created_comment = $this->repo_comment->store($data);
                $this->service_notification->store([
                    'title'=>'You have a new comment.',
                    'message'=>$user_commenting->name.' commented in your post.',
                    'id_user'=>$user_post_owner->id_user
                ]);
                DB::commit();
                $this->clearPostCommentsCache($data['id_post']);
                return $created_comment;
            /*
            *  Check if owner of post is subscribed
            */
            }else if($user_post_owner->subscribe){
                $created_comment = $this->repo_comment->store($data);
                $this->service_notification->store([
                    'title'=>'You have a new comment.',
                    'message'=>$user_commenting->name.' commented in your post.',
                    'id_user'=>$user_post_owner->id_user
                ]);
                DB::commit();
                $this->clearPostCommentsCache($data['id_post']);
                return $created_comment;
            }
            throw new CustomValidationException('User dont meet any condition to allow to comment.',[
                'User is not subscribed.',
                'User dont purchased highlight.',
                'Owner of post is not subscribed.',
            ],403);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete ($id_comment,$id_user){
        if ($id_user==null)
            throw new CustomValidationException('Usuario não informado.',[] ,400);
        DB::beginTransaction();
        try {
            $comment = $this->repo_comment->get($id_comment);
            if($comment == null)    
                throw new CustomValidationException('The $id_comment are invalid.',[
                'id_comment' => $id_comment
                ],400);
            $post = $this->repo_post->get($comment->id_post);
            if($post == null)    
                throw new CustomValidationException('The id_post are invalid.',[
                'id_post' => $comment->id_post
                ],400);
            if($id_user == $post->id_user){
                if(!$comment->visible)
                    throw new CustomValidationException('Resource already removed or not found.',[
                    'id_comment' => $comment->id_comment],404);
                $comment->visible = false;
                $comment->save();
                DB::commit();
                $this->clearPostCommentsCache($post->id_post);
                return $id_comment;
            }else if ($id_user == $comment->id_user){
                if(!$comment->visible)
                    throw new CustomValidationException('Resource already removed or not found.',[
                    'id_comment' => $comment->id_comment],404);
                $comment->visible = false;
                $comment->save();
                DB::commit();
                $this->clearPostCommentsCache($post->id_post);
                return $id_comment;
            }
            throw new CustomValidationException('User dont meet any condition to allow to comment.',[
                'User is not owner of post.',
                'User is not owner of comment.',
            ],403);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function clearPostCommentsCache($id_post){
        $cache_key = 'listcommets-'.$id_post;
        $allKeys = app('redis')->keys($cache_key.'*');
        if($allKeys != null)
            app('redis')->del($allKeys);
    }
}
?>
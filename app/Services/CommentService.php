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
    public function getAll($id_user = 0,$id_post = 0)
    {
        return $this->repo_comment->getAll($id_user,$id_post)->paginate();
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
            $post_owner = $this->repo_post->get($data['id_post']);
            $user_post_owner = $this->repo_user->get($post_owner->id_user);
            $api_settings = $this->repo_api_settings->get();
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
            *  Check if user have balance to purchase the amount of highligth are attempt to buy.
            */
            if(array_key_exists("highlight_minutes", $data)){
                $highlight_amount = $data['highlight_minutes'];
                if($user_commenting->coin_balance >= $highlight_amount){
                    $created_comment = $this->repo_comment->store($data);
                    $created_comment_id = $created_comment->id;
                    $expiration_date = new \DateTime();
                    $expiration_date->modify('+'.$highlight_amount.' minutes');
                    $created_highlight_id = $this->repo_highlight->store([
                        'id_comment'=>$created_comment_id,
                        'expiration_date'=>$expiration_date
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
                    'id_user'=>$user_post_owner->id
                ]);
                DB::commit();
                return $created_comment;
            /*
            *  Check if owner of post is subscribed
            */
            }else if($user_post_owner->subscribe){
                $created_comment = $this->repo_comment->store($data);
                $this->service_notification->store([
                    'title'=>'You have a new comment.',
                    'message'=>$user_commenting->name.' commented in your post.',
                    'id_user'=>$user_post_owner->id
                ]);
                DB::commit();
                return $created_comment;
            }
            throw new CustomValidationException('User dont meet any condition to allow comment.',[],403);
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
?>
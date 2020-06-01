<?php

namespace App\Repository;

use App\Models\Comment;

class CommentRepositoryEloquent implements CommentRepositoryInterface {
    private $model;

    public function __construct(Comment $comments)
    {
        $this->model = $comments;
    }
    public function getAll($id_user = 0,$id_post = 0)
    {
        return $this->model->select(
            'users.id_user',
            'comments.id_comment',
            'comments.id_post',
            'users.username',
            'users.subscribe',
            'highlight_comment.id_highlight_comment',
            'highlight_comment.expiration_date',
            'comments.created_at',
            'comments.message'
            )
        ->join('users', 'comments.id_user', '=', 'users.id_user')
        ->leftjoin('highlight_comment', 'comments.id_comment', '=', 'highlight_comment.id_comment')
        ->when($id_user != 0, function($query) use ($id_user){
            $query->where([
                ['users.id_user','=',$id_user]
            ]);
        })
        ->when($id_post != 0, function($query) use ($id_post){
            $query->where([
                ['comments.id_post','=',$id_post]
            ]);
        })
        ->orderBy('comments.created_at','DESC');
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }
    public function getAmountOfCommentsByIdUserInLastSeconds($id_user,$seconds){
        $date = new \DateTime();
        $date->modify('-'.$seconds.' seconds');
        $formatted_date = $date->format('Y-m-d H:i:s');
        return $this->model->where([
            ['created_at', '>',$formatted_date],
            ['id_user','=',$id_user]
            ])->count();
    }
}

?>
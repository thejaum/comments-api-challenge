<?php

namespace App\Repository;

use App\Models\Comment;

class CommentRepositoryEloquent implements CommentRepositoryInterface {
    private $model;

    public function __construct(Comment $comments)
    {
        $this->model = $comments;
    }
    public function getAll()
    {
        return $this->model->paginate(2);
    }

    public function get($id)
    {
        return $this->model->find($id);
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }
}

?>
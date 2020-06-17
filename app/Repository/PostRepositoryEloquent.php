<?php

namespace App\Repository;

use App\Models\Post;

class PostRepositoryEloquent implements PostRepositoryInterface {
    private $model;

    public function __construct(Post $posts)
    {
        $this->model = $posts;
    }
    public function getAll()
    {
        return $this->model->paginate(2);
    }

    public function get($id)
    {
        return $this->model->where('id_post' , '=', $id)->first();
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }
}

?>
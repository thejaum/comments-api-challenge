<?php

namespace App\Repository;

use App\Models\HighlightComment;

class HighlightCommentRepositoryEloquent implements HighlightCommentRepositoryInterface {
    private $model;

    public function __construct(HighlightComment $highlight_commet)
    {
        $this->model = $highlight_commet;
    }
    public function getAll()
    {
        return $this->model->paginate(2);
    }

    public function get($id)
    {
        return $this->model->where('id_highlight_comment', '=', $id)->first();
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }
}

?>
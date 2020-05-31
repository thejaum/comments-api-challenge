<?php

namespace App\Services;

use Illuminate\Support\Facades\Validator;
use App\Repository\CommentRepositoryInterface;
use App\Exceptions\CustomValidationException;

class CommentService
{
    private $repo;
    public function __construct(CommentRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }
    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function get($id)
    {
        return $this->repo->get($id);
    }

    public function store(array $data)
    {
        $validator = Validator::make($data, [
            'message' => 'required',
            'id_user' => 'required',
            'id_post' => 'required'
        ]);
        if ($validator->fails()) {
            throw new CustomValidationException('Falha na validação dos dados', $validator->errors()->toArray(),400);
        }
        $comment = json_decode(json_encode($data), FALSE);
        return $comment;
        //return $this->repo->store($data);
    }

}
?>
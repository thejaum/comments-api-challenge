<?php

namespace App\Repository;

use App\Models\Comment;

interface CommentRepositoryInterface
{
    public function __construct(Comment $comments);

    public function getAll();
	
    public function get($id);
    
    public function store(array $data);
}
?>
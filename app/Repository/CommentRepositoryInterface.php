<?php

namespace App\Repository;

use App\Models\Comment;

interface CommentRepositoryInterface
{
    public function __construct(Comment $comments);

    public function getAll($id_user = 0,$id_post = 0);
	
    public function get($id);
    
    public function store(array $data);

    public function getAmountOfCommentsByIdUserInLastSeconds($id_user,$seconds);
}
?>
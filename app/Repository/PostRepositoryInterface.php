<?php

namespace App\Repository;

use App\Models\Post;

interface PostRepositoryInterface
{
    public function __construct(Post $posts);

    public function getAll();
	
    public function get($id);
    
    public function store(array $data);
}
?>
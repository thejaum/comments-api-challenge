<?php

namespace App\Repository;

use App\Models\HighlightComment;

interface HighlightCommentRepositoryInterface
{
    public function __construct(HighlightComment $highlight_commet);

    public function getAll();
	
    public function get($id);
    
    public function store(array $data);
}
?>
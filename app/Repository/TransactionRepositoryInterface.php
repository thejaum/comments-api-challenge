<?php

namespace App\Repository;

use App\Models\Transaction;

interface TransactionRepositoryInterface
{
    public function __construct(Transaction $transactions);

    public function getAll();
	
    public function get($id);
    
    public function store(array $data);

}
?>
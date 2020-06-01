<?php

namespace App\Repository;

use App\Models\Transaction;

class TransactionRepositoryEloquent implements TransactionRepositoryInterface {
    private $model;

    public function __construct(Transaction $transactions)
    {
        $this->model = $transactions;
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
<?php

namespace App\Repository;

use App\Models\User;

class UserRepositoryEloquent implements UserRepositoryInterface {
    private $model;

    public function __construct(User $users)
    {
        $this->model = $users;
    }
    public function getAll()
    {
        return $this->model->paginate(2);
    }

    public function get($id)
    {
        return $this->model->where('id_user' , '=', $id)->first();
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->model->where('id_user' , '=', $id)->update($data);
    }

    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }
}

?>
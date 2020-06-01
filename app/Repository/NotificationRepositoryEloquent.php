<?php

namespace App\Repository;

use App\Models\Notification;

class NotificationRepositoryEloquent implements NotificationRepositoryInterface {
    private $model;

    public function __construct(Notification $notifications)
    {
        $this->model = $notifications;
    }
    public function getAll()
    {
        return $this->model->paginate(2);
    }

    public function get($id)
    {
        return $this->model->where('id_notifications' , '=', $id)->first();
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        return $this->model->where('id_notifications' , '=', $id)->update($data);
    }

    public function destroy($id)
    {
        return $this->model->find($id)->delete();
    }
}

?>
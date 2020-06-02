<?php

namespace App\Repository;

use App\Models\Notification;
use Carbon\Carbon;

class NotificationRepositoryEloquent implements NotificationRepositoryInterface {
    private $model;
    private $repo_api_settings;

    public function __construct(
        Notification $notifications,
        ApiSettingsRepositoryInterface $apiSettingsRepositoryInterface)
    {
        $this->model = $notifications;
        $this->repo_api_settings = $apiSettingsRepositoryInterface;
    }
    public function getAll($id_user = 0)
    {
        
        return $this->model->select(
            'id_user',
            'id_notifications',
            'title',
            'message',
            'visualized_date',
            'created_at',
            'updated_at'
            )
            ->when($id_user != 0, function($query) use ($id_user){
                $api_settings = $this->repo_api_settings->get();
                $now = new \DateTime();
                $now_less_expiration_hour =  $now->modify('-'.$api_settings->hours_expire_notification.' hour');
                $query->where([
                    ['id_user','=',$id_user]
                    ])
                    ->whereDate('visualized_date', '>', $now_less_expiration_hour)
                        ->orWhereNull('visualized_date');
            })
            
            ->orderBy('created_at','DESC');
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
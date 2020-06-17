<?php

namespace App\Services;

use App\Exceptions\CustomValidationException;
use App\Repository\NotificationRepositoryInterface;
use App\Repository\ApiSettingsRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class NotificationService {
    private $repo_notification;


    public function __construct(NotificationRepositoryInterface $repo_notification){
        $this->repo_notification = $repo_notification;
    }
    
    public function getAll($id_user = 0){
        $all_notifications = $this->repo_notification->getAll($id_user)->get();
        if(count($all_notifications) > 0 && $id_user != null){
            DB::beginTransaction();
            try {
                $all_notifications->map(function ($notification) {
                    $notification->visualized_date = $date_now = date("Y-m-d H:i:s");
                    $notification->save();
                });
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
        
        return $all_notifications;
    }

    public function store(array $data)
    {
        $validator = Validator::make($data, [
            'title' => 'required|max:40',
            'message' => 'required|max:255',
            'id_user' => 'required'
        ]);
        if ($validator->fails())
            throw new CustomValidationException('Falha na validação dos dados no envio da notificação ', $validator->errors()->toArray(),400);
        $created_notification = $this->repo_notification->store($data);
        return $created_notification;
    }
}

?>
<?php

namespace App\Services;

use App\Exceptions\CustomValidationException;
use App\Repository\NotificationRepositoryInterface;
use Illuminate\Support\Facades\Validator;

class NotificationService {
    private $repo_notification;

    public function __construct(
        NotificationRepositoryInterface $repo_notification){
        $this->repo_notification = $repo_notification;
    }
    public function store(array $data)
    {
        $validator = Validator::make($data, [
            'title' => 'required|max:40',
            'message' => 'required|max:255',
            'id_user' => 'required'
        ]);
        if ($validator->fails())
            throw new CustomValidationException('Falha na validação dos dados', $validator->errors()->toArray(),400);
        $created_notification = $this->repo_notification->store($data);
        return $created_notification;
    }
}

?>
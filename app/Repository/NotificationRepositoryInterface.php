<?php

namespace App\Repository;

use App\Models\Notification;

interface NotificationRepositoryInterface
{
    public function __construct(Notification $notifications,ApiSettingsRepositoryInterface $apiSettingsRepositoryInterface);

    public function getAll($id_user = 0);
	
    public function get($id);
    
    public function store(array $data);

    public function update($id, array $data);
	
	public function destroy($id);
}
?>
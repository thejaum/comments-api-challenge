<?php

namespace App\Repository;

use App\Models\Notification;

interface NotificationRepositoryInterface
{
    public function __construct(Notification $notifications);

    public function getAll();
	
    public function get($id);
    
    public function store(array $data);

    public function update($id, array $data);
	
	public function destroy($id);
}
?>
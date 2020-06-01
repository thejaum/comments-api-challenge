<?php

namespace App\Repository;

use App\Models\User;

interface UserRepositoryInterface
{
    public function __construct(User $users);

    public function getAll();
	
    public function get($id);
    
    public function store(array $data);

    public function update($id, array $data);
	
	public function destroy($id);
}
?>
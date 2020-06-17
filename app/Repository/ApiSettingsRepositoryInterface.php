<?php

namespace App\Repository;

use App\Models\ApiSettings;

interface ApiSettingsRepositoryInterface
{
    public function __construct(ApiSettings $api_settings);
	
    public function get();
    
    public function store(array $data);
}
?>
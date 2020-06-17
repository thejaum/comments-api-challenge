<?php

namespace App\Repository;

use App\Models\ApiSettings;

class ApiSettingsRepositoryEloquent implements ApiSettingsRepositoryInterface {
    private $model;

    public function __construct(ApiSettings $api_settings)
    {
        $this->model = $api_settings;
    }

    public function get()
    {   
        if(app('redis')->exists('api_settings'))
            return unserialize(app('redis')->get('api_settings'));
        $api_settings = $this->model->first();
        app('redis')->set('api_settings',serialize($api_settings));
        return $api_settings;
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }
}

?>
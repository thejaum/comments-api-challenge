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
        return $this->model->first();
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }
}

?>
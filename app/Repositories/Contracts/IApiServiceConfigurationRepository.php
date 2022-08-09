<?php

namespace App\Repositories\Contracts;

use App\Repositories\Core\IRepository;

interface IApiServiceConfigurationRepository extends IRepository {
    public function findBySlug($slug, $columns = ['*']);
}
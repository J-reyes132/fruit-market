<?php

namespace  App\Repositories\Contracts;

use App\Repositories\Core\IRepository;

interface IRequestRepository extends IRepository {
    public function getRequestByInstitution($citizenProfile,$institution);
}
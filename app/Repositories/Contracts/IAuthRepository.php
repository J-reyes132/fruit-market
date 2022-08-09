<?php

namespace App\Repositories\Contracts;

use App\Repositories\Core\IRepository;

interface IAuthRepository extends IRepository {
    public function register($params);
    public function createPasswordResetToken($request);
    public function findPasswordResetToken($token);
    public function resetPassword($request); 
}
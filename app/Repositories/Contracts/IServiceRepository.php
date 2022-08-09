<?php 

namespace App\Repositories\Contracts; 
use App\Repositories\Core\IRepository;

interface IServiceRepository extends IRepository {
    public function searchByKeywords($keywords);
}
<?php

namespace App\Repositories\Core;

use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


interface IRepository {
    
    public function getFieldsSearchable();
    public function model();
    public function makeModel();
    public function paginate($perPage = 10, $columns = ['*']);
    public function paginateByQuery($search = [], $perPage = 10, $columns = ['*']);
    public function search($search = [], $skip = null, $limit = null);
    public function allQuery($search = [], $skip = null, $limit = null);
    public function all($search = [], $skip = null, $limit = null, $columns = ['*']);
    public function create($input);
    public function commit($query);
    public function find($id, $columns = ['*']);
    public function findBy($column, $search, $columns = ['*']);
    public function update($input, $id);
    public function delete($id);
    public function count();
    public function with($relations);
}
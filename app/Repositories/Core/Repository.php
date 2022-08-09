<?php

namespace App\Repositories\Core;

use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class Repository implements IRepository {

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     *
     * @throws \Exception
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Get searchable fields array
     *
     * @return array
     */
    abstract public function getFieldsSearchable();

    /**
     * Configure the Model
     *
     * @return string
     */
    abstract public function model();

    /**
     * Make Model instance
     *
     * @throws \Exception
     *
     * @return Model
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Paginate records for scaffold.
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage = 10, $columns = ['*'])
    {
        $query = $this->allQuery();

        return $query->paginate($perPage, $columns);
    }

    /**
     * Paginate records for scaffold by query.
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateByQuery($search = [], $perPage = 10, $columns = ['*'])
    {
        $query = $this->allQuery($search);

        return $query->paginate($perPage, $columns);
    }

    /**
     * search records for scaffold.
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search($search = [], $skip = null, $limit = null)
    {
        $query = $this->model->newQuery();

        if (count($search)) {
            foreach($search as $key => $value) {
                $query->when(in_array($key, $this->getFieldsSearchable()), function($query) use($key, $value) {
                    $query->when(is_array($value), function($query) use($key, $value) {
                        $query->whereIn($key, $value);
                    });
                    $query->when(!is_array($value), function($query) use($key, $value) {
                        $query->where($key, 'LIKE', '%' . $value . '%');
                    });
                    
                });
            }
        }

        if (!is_null($skip)) {
            $query->skip($skip);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Build a query for retrieving all records.
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function allQuery($search = [], $skip = null, $limit = null)
    {
        $query = $this->model->newQuery();

        if (count($search)) {
            foreach($search as $key => $value) {
                if (in_array($key, $this->getFieldsSearchable())) {
                    $query->where($key, $value);
                }
            }
        }

        if (!is_null($skip)) {
            $query->skip($skip);
        }

        if (!is_null($limit)) {
            $query->limit($limit);
        }

        return $query;
    }

    /**
     * Retrieve all records with given filter criteria
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @param array $columns
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all($search = [], $skip = null, $limit = null, $columns = ['*'])
    {
        $query = $this->allQuery($search, $skip, $limit);

        return $query->get($columns);
    }

    /**
     * Create model record
     *
     * @param array $input
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function create($input)
    {
        $model = $this->model->newInstance($input);

        try {
            DB::transaction(function() use ($model) {
                $model->save();
            });

            DB::commit();

            return $model;
            
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    /**
     * Commit or rollback record to DB
     *
     * @param array $query
     * @return query
     */
    public function commit($query)
    {
        try {
            DB::transaction(function() use ($query) {
                $query;
            });

            DB::commit();

            return $query;
            
        } catch (\Throwable $th) {
            DB::rollback();
        }
    }

    /**
     * Find model record for given id
     *
     * @param int $id
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function find($id, $columns = ['*'])
    {
        $query = $this->model->newQuery();

        return $query->find($id, $columns);
    }

    /**
     * Update model record for given id
     *
     * @param array $input
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model
     */
    public function update($input, $id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $model->fill($input);

        try {
            DB::transaction(function() use ($model) {
                $model->save();
            });

            DB::commit();

            return $model;
            
        } catch (\Throwable $th) {
            DB::rollback();
        }
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function delete($id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        try {
            DB::transaction(function() use ($model) {
                $model->delete();
            });

            DB::commit();

            return $model;
            
        } catch (\Throwable $th) {
            DB::rollback();
        }
    }

    /**
     * Count all records of model
     *
     * @return int
     */
    public function count() 
    {
        return $this->model->all('id')->count();
    }

    /**
     * Return model by column
     */
    public function findBy($column, $search, $columns = ['*']) 
    {
        $query = $this->model->newQuery();

        return $query->where($column, $search)->first($columns);
    }

    public function with($relations)
    {
        return $this->model->with($relations);
    }
}
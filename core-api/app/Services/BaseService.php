<?php

namespace App\Services;

namespace App\Services;

use App\Repositories\BaseRepository;

abstract class BaseService
{
    protected BaseRepository $repository;

    public function __construct(BaseRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function store(array $data)
    {
        return $this->repository->store($data);
    }

    public function one($id, array $relations = [])
    {
        return $this->repository->one($id, $relations);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function deleteBy(array $conditions, array $relations = [])
    {
        return $this->repository->deleteBy($conditions, $relations);
    }

    public function updateOrCreate(array $data, array $where)
    {
        return $this->repository->updateOrCreate($data, $where);
    }

    public function findBy(array $conditions, array $relations = [])
    {
        return $this->repository->findBy($conditions, $relations);
    }

    public function findCoupleBy(array $conditions, array $relations = [])
    {
        return $this->repository->findCoupleBy($conditions, $relations);
    }

    public function findOrCreate(array $conditions)
    {
        return $this->repository->findOrCreate($conditions);
    }
}


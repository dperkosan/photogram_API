<?php

namespace App\Interfaces;


interface UserRepositoryInterface
{
    public function getAll();
    public function getById($id);
    public function store($data);
}
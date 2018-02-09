<?php

namespace App\Repositories;

class Repository
{
    protected function calcOffset($amount, $page)
    {
        return ($page - 1) * $amount;
    }
}
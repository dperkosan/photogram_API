<?php

use Illuminate\Database\Seeder;

class BaseTableSeeder extends Seeder
{
    protected function getDatePeriod($recurrences, $interval = 'PT1S')
    {
        return new \DatePeriod(
          new \DateTime(),
          new \DateInterval($interval),
          $recurrences
        );
    }
}
<?php

namespace App;

class TestOverseer
{
    /**
     * @var static
     */
    protected static $instance;

    protected $isAppCreated;

    protected function __construct()
    {
        $this->isAppCreated = false;
    }

    protected static function getInstance() : self
    {
        if (empty(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function getIsAppCreated()
    {
        return $this->isAppCreated;
    }

    public function setIsAppCreated($isAppCreated)
    {
        $this->isAppCreated = $isAppCreated;
    }
}
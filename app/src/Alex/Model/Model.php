<?php

namespace Alex\Model;

abstract class Model
{
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
}
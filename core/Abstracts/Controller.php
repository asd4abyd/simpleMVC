<?php

namespace Core\Abstracts;

use Core\Libraries\Config;
use Core\Libraries\DB;
use Core\Libraries\Request;

abstract class Controller
{
    protected $request;
    private $config;
    private $db;

    public function __construct(Request $request, Config $config)
    {
        $this->request = $request;
        $this->config = $config;
        $this->db = new DB($config);
    }

    protected function config($key){
        $config = $this->config;
        return $config($key);
    }

    protected function loadModel($model, $alias=null){
        if(is_null($alias)){
          $alias=$model;
        }

        if(!property_exists($this, $alias)){
            $model= '\App\Models\\'.$model;

            $this->$alias = new $model($this->request, $this->config, $this->db);
        }

        return true;
    }
}
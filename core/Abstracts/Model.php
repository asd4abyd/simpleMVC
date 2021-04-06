<?php

namespace Core\Abstracts;

use Core\Libraries\Config;
use Core\Libraries\DB;
use Core\Libraries\Request;

abstract class Model
{
    protected $request;
    protected $db;
    private $config;

    public function __construct(Request $request, Config $config, DB $db)
    {
        $this->request = $request;
        $this->config = $config;
        $this->db = $db;
    }

    protected function config($key){
        $config = $this->config;
        return $config($key);
    }
}
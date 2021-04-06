<?php

namespace Core\Libraries;

class Request
{
    private $data=[];
    private $post;
    private $get;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
    }

    public function get($key, $default=null)
    {
        return $this->get[$key]??$default;
    }

    public function post($key, $default=null)
    {
        return $this->post[$key]??$default;
    }

    public function __set($name, $value)
    {
        $this->data[$name]=$value;
    }

    public function __get($name)
    {
        return $this->data[$name] ?? null;
    }
}
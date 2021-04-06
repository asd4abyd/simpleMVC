<?php

namespace Core\Interfaces;

use Core\Libraries\Request;

interface Middleware
{
    public function handle(Request &$request);
}
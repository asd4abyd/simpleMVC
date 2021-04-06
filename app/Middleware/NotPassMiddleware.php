<?php

namespace App\Middleware;

use Core\Interfaces\Middleware;
use Core\Libraries\Request;

class NotPassMiddleware implements Middleware
{

    public function handle(Request &$request)
    {
        $request->abu_abyd='hi';

        return true;
    }
}
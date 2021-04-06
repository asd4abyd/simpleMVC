<?php

Class ClassNotFount extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message.' class not found', $code, $previous);
    }
}

spl_autoload_register(function ($class_name) {

    $path = explode('\\', $class_name, 2);

    $classPath=false;

    if (isset($path[1])) {
        switch ($path[0]) {
            case 'App':
                $classPath = APP_PATH . $path[1].'.php';
                break;

            case 'Core':
                $classPath = CORE_PATH . $path[1].'.php';
                break;

            default:
        }
    }

    $classPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $classPath);


    if($classPath && file_exists($classPath)){
        include_once $classPath;
        return;
    }

    throw new ClassNotFount($class_name);

});


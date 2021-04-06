<?php

namespace Core\Libraries;

class View
{
    private $viewFile;
    private $vars;

    public function __construct($viewFile, $vars)
    {
        $this->viewFile = $viewFile;
        $this->vars = $vars;
    }

    public static function load($view, $vars = [], $return = false)
    {

        $view = trim($view, '.');
        $view = preg_replace('/[\.]+/', '.', $view);
        $view = str_replace('.', DIRECTORY_SEPARATOR, $view);

        $view = $view . '.php';


        if (file_exists(RESOURCES_PATH . 'views/' . $view)) {

            $viewInstance = new self(RESOURCES_PATH . 'views/' . $view, $vars);

            if ($return) {
                return $viewInstance->render();
            }

            return $viewInstance;
        }

        return null;
    }

    public function render()
    {
        extract($this->vars);
        ob_clean();
        ob_start();

        include $this->viewFile;

        $contents = ob_get_contents();

        ob_end_clean();

        return $contents;
    }
}
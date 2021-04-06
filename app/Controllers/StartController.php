<?php

namespace App\Controllers;

use App\Models\AaModel;
use Core\Abstracts\Controller;
use Core\Libraries\View;

/**
 * Class StartController
 * @package App\Controllers
 *
 * @property AaModel $aa
 */
class StartController extends Controller
{
    public function index()
    {
        return ['hi all'];
    }

    public function name($id)
    {

        $this->loadModel('AaModel', 'aa');


        return View::load('start', ['name'=> $this->aa->getName($id)]);
    }

}
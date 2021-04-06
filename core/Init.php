<?php

use \Core\Core;

foreach (glob(HELPERS_PATH.'*.php') as $helperFile){
    include_once $helperFile;
}


$core = new Core();

$core->fireController();


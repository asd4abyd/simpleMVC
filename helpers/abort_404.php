<?php

if(!function_exists('abort_404')){
    function abort_404($message=''){
        http_response_code(404);
        die($message);
    }
}

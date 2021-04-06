<?php

if(!function_exists('dd')){
    function dd(...$any){
        var_dump(...$any);
        die(0);
    }
}

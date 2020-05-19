<?php

// /home/sujianhui/CLionProjects/output/php7_1/bin/php
if(extension_loaded("helloworld")){
    $module = 'helloworld';
    $functions = get_extension_funcs($module);

    foreach($functions as $e){
        echo "function Name is : ".$e."\n";
        echo "function return Value is : ".$e(" - self input - ")."\n";;
    }

} else {
    echo "load extension fail \n";
}
//echo zif_confirm_helloworld_compiled("sss");

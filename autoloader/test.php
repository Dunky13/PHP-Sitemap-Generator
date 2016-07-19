<?php
$test = "greet";
$$test = function($name)
{
    printf("Hello %s\r\n", $name);
};

$greet('World');
$greet('PHP');

?>
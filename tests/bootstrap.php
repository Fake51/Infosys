<?php

require __DIR__ . '/../include/framework/autoloader.php';

$paths = [
          __DIR__ . '/../include/framework/',
          __DIR__ . '/../include/entities/',
         ];

$autoloader = new Autoloader($paths);
spl_autoload_register([$autoloader, 'autoloader']);

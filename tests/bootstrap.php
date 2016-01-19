<?php

require __DIR__ . '/../include/framework/exception.php';
require __DIR__ . '/../include/framework/autoloader.php';

define('ENTITY_FOLDER', __DIR__ . '/../include/entities/');

$paths = [
          __DIR__ . '/../include/framework/',
          __DIR__ . '/../include/entities/',
          __DIR__ . '/../include/controllers/',
          __DIR__ . '/../include/models/',
         ];

$autoloader = new Autoloader($paths);
spl_autoload_register([$autoloader, 'autoloader']);

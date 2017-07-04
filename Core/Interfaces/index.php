<?php

namespace Interfaces;

@session_start();

use Classes\GShark;

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', __DIR__.DS);

require_once __DIR__.'/Boot.php';

Boot::register();

$boot = new GShark();
$boot->routing();

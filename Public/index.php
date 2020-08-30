<?php
session_start();
define('ROOT',dirname(__DIR__).DIRECTORY_SEPARATOR);
define('APP', ROOT);

require(ROOT . 'Config/core.php');
require (ROOT . 'vendor/autoload.php');



/** Autoload any classes that are required **/
 
function autoload($className) {

    if (file_exists(ROOT . strtolower($className) . '.php')) {
        require_once(ROOT . strtolower($className) . '.php');
    } else if (file_exists(CORE . strtolower($className) . '.php')) {
        require_once(CORE . strtolower($className) . '.php');
    }else if (file_exists(LIBRARY . strtolower($className) . '.php')) {
        require_once(LIBRARY . strtolower($className) . '.php');
    } else if (file_exists(CONTROLLER .  strtolower($className) . '.php')) {
        require_once(CONTROLLER .  strtolower($className) . '.php');
    } else if (file_exists(MODEL .  strtolower($className) . '.php')) {
        require_once(MODEL .  strtolower($className) . '.php');
    } else {
        /* Error Generation Code Here */
    }
}
spl_autoload_register("autoload",false);
/**
 * Load any .env file. See /.env.example.
 */

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);

try {
  $dotenv->load();
}
catch (InvalidPathException $e) {
  // Do nothing. Production environments rarely use .env files.
}

$dispatch = new Dispatcher();
$dispatch->dispatch();
?>
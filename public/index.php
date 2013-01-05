<?php

// Defined global variables
define("PUBLIC_PATH", dirname(__FILE__));
define("TEMPLATE_PATH", dirname(__FILE__) . '/templates');
define('TEMPLATE_URL', '/templates');

define("IMAGE_PATH", dirname(__FILE__) . '/images');
define("IMAGE_URL", '/whopaid/public/images');

define('SCRIPT_PATH', dirname(__FILE__) . '/scripts');
define('SCRIPT_URL', '/whopaid/public' . '/scripts');

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH',
              realpath(dirname(dirname(__FILE__)) . '/application'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV',
              (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV')
                                         : 'production'));

// Typically, you will also want to add your library/ directory
// to the include_path, particularly if it contains your ZF installed
set_include_path(implode(PATH_SEPARATOR, array(
    dirname(dirname(__FILE__)) . '/library',
    get_include_path(),
)));
/** Zend_Application */
require_once 'Zend/Application.php';

$environment = APPLICATION_ENV;
$options = APPLICATION_PATH . '/configs/application.ini';
$application = new Zend_Application($environment, $options);

$application->bootstrap()->run();




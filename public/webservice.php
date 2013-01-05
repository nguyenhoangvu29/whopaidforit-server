<?php
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
require_once('MyWebService.php');

    require_once('Zend/Soap/AutoDiscover.php');

    $auto = new Zend_Soap_AutoDiscover();
    $auto->setClass('MyWebService');
    $auto->handle();
?>
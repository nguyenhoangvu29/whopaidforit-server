<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap{

 	protected function _initAutoload()
   {
	  Zend_Session::start();
      $autoloader = new Zend_Application_Module_Autoloader(array(
                   'namespace' => '',
                  'basePath' => APPLICATION_PATH,

      ));
     
      return $autoloader;

   }

   public function _initPlugin(){

   		$front = Zend_Controller_Front::getInstance();
   		$front->registerPlugin(new ZendSlicehtml_System_Plugin());
   		
   }
   protected function _initDatabase()
   {
   	$db = new Zend_Db_Adapter_Pdo_Mysql(array(
   			'host'     => 'localhost',
   			'username' => 'nghexayd_whopaid',
   			'password' => 'wp123456',
   			'dbname'   => 'nghexayd_whopaid'
   	));
   	Zend_Registry::set('Zend_Db', $db);
   }

}
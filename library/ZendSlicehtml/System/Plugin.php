<?php
    class ZendSlicehtml_System_Plugin extends Zend_Controller_Plugin_Abstract{
        
        public function routeStartup(Zend_Controller_Request_Abstract $request){
                //echo __METHOD__;
        }
        
        public function routeShutdown(Zend_Controller_Request_Abstract $request){            
        }
        
        public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request){            
        }
        
        public function preDispatch(Zend_Controller_Request_Abstract $request){
          $controller = $this->_request->getParam('controller');
          $flagAdmin = false;
          if($controller == "admin"){
              $flagAdmin = true;
          }
          else{
              $tmp = explode('-', $controller);
              if($tmp[0] == "admin"){
                  $flagAdmin = true;
              }
          }
          if($flagAdmin == true){

          				if(isset($_SESSION['isLogin'])){
        
          					
          				}else{

          					$this->_request->setModuleName("admin");
          					$this->_request->setControllerName("admin-user");
          					$this->_request->setActionName("logon");
          				}
          	
                        /*$auth = Zend_Auth::getInstance(); //check login
                        if(!$auth->hasIdentity()){
                        		//custom!
                        		
                                $this->_request->setModuleName("admin");
                                $this->_request->setControllerName("admin-user");
                                $this->_request->setActionName("logon");
                        }
                        else{
                        	
                                $this->_request->setModuleName("default");
                            	$this->_request->setControllerName("public");
                            	$this->_request->setActionName("no-access");
                        }*/
          }
        }
        
        public function postDispatch(Zend_Controller_Request_Abstract $request){            
        }
        
        public function dispatchLoopShutdown(){            
        }
        
    }
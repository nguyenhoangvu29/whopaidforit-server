<?php
class Admin_IndexController extends ZendSlicehtml_Controller_Action{

 		public function init(){
 			
 				try{
                parent::init();         
                $template_path = TEMPLATE_PATH . '/admin/system/';
                $this->loadTemplate($template_path);
 				}catch(Exception $e){
 					echo $e;
 				}
               
        }
       
        public function indexAction(){    
        	                 
                //get data
        }
        public function viewAction(){
        
        
        }
	

}
<?php
class ZendSlicehtml_Controller_Action extends Zend_Controller_Action{
        
		
        public function loadTemplate($template_path,$fileConfig = 'template.ini',$sectionConfig = 'template'){
                
                $this->view->headTitle('',true);
                $this->view->headMeta()->getContainer()->exchangeArray(array());
                $this->view->headLink()->getContainer()->exchangeArray(array());
                $this->view->headScript()->getContainer()->exchangeArray(array());
                
                $file = $template_path . $fileConfig; // TEMPLATE_PATH . '/admin/system/template.ini';
                $config  = new Zend_Config_Ini($file,$sectionConfig);
                $config = $config->toArray();
                
              
                $baseUrl = $this->_request->getBaseUrl();
                
                $templateUrl    = $baseUrl . $config['url'];
                $cssUrl                 = $templateUrl . $config['dirCss'];
                $jsUrl                  = $templateUrl . $config['dirJs'];
                $imgUrl                 = $templateUrl . $config['dirImg'];
                
                $this->view->templateUrl        = $templateUrl;
                $this->view->cssUrl             = $cssUrl;
                $this->view->jsUrl                      = $jsUrl;
                $this->view->imgUrl                     = $imgUrl;
                
                $this->view->headTitle()->set($config['title']);
                if(count($config['metaHttp'])>0){
                        foreach ($config['metaHttp'] as $key => $metaHttp){
                                $tmp = explode('|',$metaHttp);
                                $this->view->headMeta()->appendHttpEquiv($tmp[0],$tmp[1]);
                        }
                        
                }
                
                if(count($config['metaName'])>0){
                        foreach ($config['metaName'] as $key => $metaName){
                                $tmp = explode('|',$metaName);
                                $this->view->headMeta()->appendName($tmp[0],$tmp[1]);
                        }
                        
                }
                
                if(count($config['fileCss'])>0){
                        foreach ($config['fileCss'] as $key => $css){
                                $this->view->headLink()->appendStylesheet($cssUrl . $css, 'screen');
                        }
                }
                
                if(count($config['fileJs'])>0){
                        foreach ($config['fileJs'] as $key => $js){
                                $this->view->headScript()->appendFile($jsUrl . $js,'text/javascript');
                        }
                }
                $option = array(
                                                'layoutPath'=> $template_path,
                                                'layout'=> 'index',
                
                                                );
   
                Zend_Layout::startMvc($option);
                
        }
}
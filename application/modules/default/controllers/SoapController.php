<?php
//echo realpath(APPLICATION_PATH . '/../library/');
//require_once realpath(APPLICATION_PATH . '/../library/').'/Soaptest.php';

class SoapController extends Zend_Controller_Action
{
	private $_WSDL_URI = "http://localhost/zftutorial/public/index.php/soap?wsdl";
	//private $_WSDL_URI = "http://webservice.ictp-dev.nl/public/index.php/soap?wsdl";
	
 	public function init()
    {	 
    }

    public function indexAction()
    {    
    	$this->_helper->viewRenderer->setNoRender();
    	$items  = new Model_Item();
    	
    	$data  = $items->fetchAll($items->select());
    	print_r($data);
    	/*;
    	if(isset($_GET['wsdl'])) {
    		//return the WSDL
    		$this->hadleWSDL();
		} else {
			//handle SOAP request
    		$this->handleSOAP();
		}*/
    }

	private function hadleWSDL() {
		$autodiscover = new Zend_Soap_AutoDiscover();
    	$autodiscover->setClass('Soaptest');
    	$autodiscover->handle();
	}
    
	private function handleSOAP() {
		$soap = new Zend_Soap_Server($this->_WSDL_URI); 
    	$soap->setClass('Soaptest');
    	$soap->handle();
	}
    

}


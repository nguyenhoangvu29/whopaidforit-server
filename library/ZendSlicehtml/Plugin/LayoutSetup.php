<?php
class ZendSlicehtml_Plugin_LayoutSetup extends Zend_Controller_Plugin_Abstract {
	public function preDispatch(Zend_Controller_Request_Abstract $request) {

		$module = $request->getModuleName ();
		$layout = Zend_Layout::getMvcInstance ();

		if ($module == "admin") {
			$layout->setLayout ( "admin" );
		} else {
			$layout->setLayout ( "default" );
		}
	}

}
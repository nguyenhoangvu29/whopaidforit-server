<?php
class ZendSlicehtml_System_Auth {
	
	public function Login($arrParam, $option = null) {
		if ($arrParam ["username"] == 'Admin' && $arrParam ["password"] == 'wh0p@!df0r') {
			
			$_SESSION ['isLogin'] = 1;
			
			return true;
		} else {
			unset ( $_SESSION ['isLogin'] );
		
		}
		/*
		 * $db = Zend_Registry::get ( "Zend_Db" ); $authAdapter = new
		 * Zend_Auth_Adapter_DbTable ( $db, 'users', 'mail_address', 'password'
		 * ); $select = $authAdapter->getDbSelect (); //
		 * $select->where('status=1'); $username = $arrParam ["username"];
		 * $password = md5 ( $arrParam ["password"] ); $authAdapter->setIdentity
		 * ( $username ); $authAdapter->setCredential ( $password ); $auth =
		 * Zend_Auth::getInstance (); $result = $auth->authenticate (
		 * $authAdapter ); $flagAcces = false; if (! $result->isValid ()) {
		 * $message = $result->getMessages (); echo current ( $message ); } else
		 * { $returnColumn = null; $omiColumns = array ( 'password' ); $data =
		 * $authAdapter->getResultRowObject ( $returnColumn, $omiColumns );
		 * $auth->getStorage ()->write ( $data ); $flagAcces = true; } return
		 * $flagAcces;
		 */
		return false;
	}
	public function Logout() {
		$auth = Zend_auth::getInstance ();
		$auth->clearIdentity ();
	}
}

<?php
class Admin_AdminUserController extends ZendSlicehtml_Controller_Action {
	
	protected $_arrParam = '';
	public function init() {
		
		parent::init ();
		$template_path = TEMPLATE_PATH . '/admin/system/';
		$this->loadTemplate ( $template_path );
		$this->_arrParam = $this->_request->getParams ();

	}
	
	
	public function exitsAction() {
	  
		if (isset ( $_SESSION ['isLogin'] )) {
			unset ( $_SESSION ['isLogin'] );
		}
		$this->_redirect ( "/admin/admin-user/logon" );
	}
	
	
	public function logonAction() {
		
		$this->_helper->layout ()->setLayout ( 'login' );
		if ($this->_request->isPost ()) {
			
			$auth = new ZendSlicehtml_System_Auth ();
			if ($auth->Login ( $this->_arrParam )) {
				$this->_redirect ( "/admin/admin-user/index" );
			} else {
				$this->_redirect ( "/admin/admin-user/logon" );
			}
		}
	}
	
	public function indexAction() {
		$users = new Admin_Model_User ();
		$rows = $users->fetchAll ( $users->select () );
		$result = array ();
		foreach ( $rows as $row ) {
			$result [] = array (
					'id' => $row->id,
					'name' => $row->name,
					'mail_address' => $row->mail_address,
					'created' => date ( 'd-m-Y', strtotime ( $row->created ) ) 
			);
		}
		
		$this->view->Items = $result;
	}
	/*
	 * param: id return: object description:
	 */
	public function getuserinfoAction() {
		$user_id = $this->_request->getParam ( 'id' );
		
		$table = new Model_User (); // user_pieces là class extends
		                            // Zend_Db_Table_Abstract
		$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
		$select->setIntegrityCheck ( false )->where ( 'users.id = ?', $user_id );
		// print_r($select->__toString());die;
		$rows = $table->fetchAll ( $select );
		$result = array ();
		foreach ( $rows as $row ) {
			$result ['name'] = 'User';
			$result ['description'] = 'User infomation by ID';
			$result ['return'] = 'Object';
			$result ['params'] = array (
					'type' => 'number',
					'name' => 'id',
					'required' => true 
			);
			$result ['properties'] [] = array (
					'id' => $row->id,
					'name' => $row->name,
					'first_name' => $row->first_name,
					'middle_name' => $row->middle_name,
					'last_name' => $row->last_name,
					'mail_address' => $row->mail_address,
					'created' => date ( 'd-m-Y', strtotime ( $row->created ) ) 
			);
		}
		
		echo json_encode ( $result );
		die ();
	}
	public function getmemberbyeventAction() {
		$event_id = $this->_request->getParam ( 'event_id' );
		
		$table = new Model_User (); // user_pieces là class extends
		                            // Zend_Db_Table_Abstract
		$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
		$select->setIntegrityCheck ( false )->join ( 'user_event', 'users.id = user_event.user_id', array (
				'*',
				'users.id as user_id' 
		) )->where ( 'user_event.event_id = ?', $event_id );
		$rows = $table->fetchAll ( $select );
		$result = array ();
		foreach ( $rows as $row ) {
			$result [] = array (
					'user_id' => $row->user_id,
					'name' => $row->name,
					'mail_address' => $row->mail_address,
					'event_id' => $row->event_id,
					'created' => date ( 'd-m-Y', strtotime ( $row->created ) ) 
			);
		}
		
		echo json_encode ( $result );
		die ();
	}
	public function getmemberbyexpendsesAction() {
		$expenses_id = $this->_request->getParam ( 'expenses_id' );
		$result = array ();
		if (isset ( $expenses_id )) {
			
			$table = new Model_User ();
			$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
			$select->setIntegrityCheck ( false )->join ( 'participants as pa', 'users.id = pa.user_id', array (
					'*',
					'users.id as user_id' 
			) )->where ( 'pa.expenses_id = ?', $expenses_id );
			// print_r($select->__toString());die;
			$rows = $table->fetchAll ( $select );
			
			foreach ( $rows as $row ) {
				$result [] = array (
						'user_id' => $row->user_id,
						'name' => $row->name,
						'mail_address' => $row->mail_address,
						'expenses_id' => $row->expenses_id,
						'created' => date ( 'd-m-Y', strtotime ( $row->created ) ) 
				);
			}
		} else {
			$result ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $result );
		die ();
	}
	public function getmemberbyuserAction() {
		$user_id = $this->_request->getParam ( 'user_id' );
		
		$table = new Model_User (); // user_pieces là class extends
		                            // Zend_Db_Table_Abstract
		$select = $table->select ( 'id', 'name', 'mail_address' );
		$select->setIntegrityCheck ( false )->join ( 'user_event', 'users.id = user_event.user_id' )->where ( 'user_event.event_id in (SELECT ue2.event_id FROM `user_event` ue2 WHERE ue2.user_id=?)', $user_id )->group ( 'users.id' )->order ( 'users.name' );
		/*
		 * $select = $table->select()->from('users', array('id','mail_address'))
		 * ->join('user_event', 'users.id = user_event.user_id')
		 * ->where('user_event.user_id = ?',$user_id );
		 */
		$rows = $table->fetchAll ( $select );
		$result = array ();
		foreach ( $rows as $row ) {
			$result [] = array (
					'user_id' => $row->user_id,
					'name' => $row->name,
					'mail_address' => $row->mail_address,
					'event_id' => $row->event_id,
					'created' => date ( 'd-m-Y', strtotime ( $row->created ) ) 
			);
		}
		$sql = $select->__toString ();
		// echo $sql;exit;
		echo json_encode ( $result );
		die ();
	}
	public function addAction() {
		$users = new Model_User ();
		$name = $this->_request->getParam ( 'name' );
		$mail_address = $this->_request->getParam ( 'mail_address' );
		$password = $this->_request->getParam ( 'password' );
		$id = $this->_request->getParam ( 'id' );
		$data = array (
				'name' => $name,
				'mail_address' => $mail_address,
				'password' => md5 ( $password ),
				'created' => date ( 'Y-m-d h:i:s' ) 
		);
		$return = $users->saveUser ( $data, $id );
		echo json_encode ( $return );
		die ();
	}
	public function updateAction() {
		$users = new Model_User ();
		$name = $this->_request->getParam ( 'name' );
		$email = $this->_request->getParam ( 'email' );
		$user_id = $this->_request->getParam ( 'user_id' );
		$id = $this->_request->getParam ( 'id' );
		$data = array (
				'name' => $name,
				'mail_address' => $email 
		);
		$return = $users->saveUser ( $data, $id );
		echo json_encode ( $return );
		die ();
	}
	public function addmemberAction() {
		$user = new Model_User ();
		$name = $this->_request->getParam ( 'name' );
		$email = $this->_request->getParam ( 'email' );
		$eventid = $this->_request->getParam ( 'event_id' );
		$userid = $this->_request->getParam ( 'user_id' );
		$data = array (
				'name' => $name,
				'event_id' => $eventid,
				'user_id' => $userid,
				'mail_address' => $email,
				'created' => date ( 'Y-m-d h:i:s' ) 
		);
		
		$return = $user->addMember ( $data );
		echo json_encode ( $return );
		die ();
		// echo $return; die;
	}
	function loginAction() {
		$model_user = new Model_User ();
		$model_event = new Model_Event ();
		$username = $this->_request->getParam ( 'username' );
		$password = $this->_request->getParam ( 'password' );
		$select = $model_user->select ()->where ( "mail_address='$username' and password='" . md5 ( $password ) . "'" );
		$result = $model_user->fetchAll ( $select );
		if (count ( $result )) {
			// return user
			$user_id = $result [0]->id;
			$return = array (
					'id' => $result [0]->id,
					'name' => $result [0]->name,
					'mail_address' => $result [0]->mail_address 
			);
			$return ['func'] = '1';
			// get latest event
			$select = $model_event->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array (
					'id' 
			) )->setIntegrityCheck ( false )->join ( 'user_event', 'user_event.event_id = event.id', array () )->where ( "user_event.user_id = '" . $result [0]->id . "'" )->order ( 'event.id desc' );
			$result = $model_event->fetchAll ( $select );
			$event_id = $result [0]->id;
			$return ['event_id'] = $result [0]->id;
			$return ['event_name'] = $result [0]->name;
			$return ['page'] = 'addentry';
		} else {
			// check email exist
			$select = $model_user->select ()->where ( "mail_address='$username'" );
			$result = $model_user->fetchAll ( $select );
			if (count ( $result )) { // exist email, wrong password
				if ($result [0]->password) { // wrong password
					$return = array (
							'id' => "0" 
					);
					$return ['func'] = '0';
				} else {
					// update password
					$user_id = $result [0]->id;
					$return = array (
							'id' => $result [0]->id,
							'name' => $result [0]->name,
							'mail_address' => $result [0]->mail_address 
					);
					$obj ['password'] = md5 ( $password );
					// $model_user->saveUser($obj,$user_id);
					// count number event of that user
					$select = $model_event->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array (
							'id' 
					) )->setIntegrityCheck ( false )->join ( 'user_event', 'user_event.event_id = event.id', array () )->where ( "user_event.user_id = '" . $user_id . "'" )->order ( 'event.id desc' );
					
					$result = $model_event->fetchAll ( $select );
					if (count ( $result ) == 1) {
						$return ['event_id'] = $result [0]->id;
						$return ['event_name'] = $result [0]->name;
						$return ['page'] = 'listentries';
					} else {
						$return ['page'] = 'events';
					}
				}
			} else {
				// create new account and return user_id
				$data = array (
						'mail_address' => $username,
						'password' => md5 ( $password ),
						'created' => date ( 'Y-m-d h:i:s' ) 
				);
				$value = $model_user->saveUser ( $data );
				$return ['func'] = '2';
				$return ['id'] = $value ['id'];
				$return ['page'] = 'addevent';
			}
		}
		echo json_encode ( $return );
		die ();
	}
	public function deleteAction() {
		$return = array ();
		try {
			$users = new Model_User ();
			$id = $this->_request->getParam ( 'id' );
			$users->delete ( 'id=' . $id );
			$db = new Zend_Db_Table ( array (
					'name' => 'user_event' 
			) );
			$db->delete ( 'user_id=' . $id );
			$return ['success'] = 1;
		} catch ( Exception $e ) {
			$return ['success'] = 0;
		}
		echo json_encode ( $return );
		die ();
	}
}
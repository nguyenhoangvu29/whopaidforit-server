<?php
class Admin_AdminExpensesController extends ZendSlicehtml_Controller_Action {
	
	public function init(){
			
		parent::init();
		$template_path = TEMPLATE_PATH . '/admin/system/';
		$this->loadTemplate($template_path);
	
		$this->_arrParam                        = $this->_request->getParams();
		$this->_currentController       = '/' . $this->_arrParam['module'] . '/' . $this->_arrParam['controller'];
		$this->_actionMain                      = '/' . $this->_arrParam['module'] . '/' . $this->_arrParam['controller'] . '/index';
	
		$this->view->arrParam                   = $this->_arrParam;
		$this->view->currentController  = $this->_currentController;
	}
	
	
	public function indexAction() {
		$user_id = $this->_request->getParam ( 'user_id' );
		$event_id = $this->_request->getParam ( 'event_id' );
		$table = new Admin_Model_Expenses (); // user_pieces là class extends
		// Zend_Db_Table_Abstract
		$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
		$select->setIntegrityCheck ( false )->join ( 'event', 'event.id = expenses.event_id', array (
				'*',
				'expenses.description as expenses_des',
				' IFNULL((select count(ue.user_id) from participants ue where ue.expenses_id = expenses.id),0) as  totalpeople'
		) )->join ( 'users', 'expenses.user_id = users.id', array (
				'users.name as user_name',
				'expenses.id as expenses_id'
		) );
		if(isset ( $user_id )){
			
			$select->where ('expenses.user_id = ?', $user_id);
		}
		if(isset ( $event_id )){
				
			$select->where ( 'expenses.event_id = ?', $event_id );
			$this->view->assign('event_id', $event_id);
		}
		//echo $select->__toString();die;
		$rows = $table->fetchAll ( $select );
			
			
		foreach ( $rows as $row ) {
		
			$result [] = array (
					'id' => $row->expenses_id,
					'eventname' => $row->name,
					'description' => $row->expenses_des,
					'event_id' => $row->event_id,
					'user_name' => $row->user_name,
					'totalamount' => $row->amount,
					'totalpeople' => $row->totalpeople,
					'settled' => $row->settled,
					'name_paidby' => $row->user_name,
					'expenses_type_id' => $row->expenses_type_id,
					'created' => date ( 'd-m-Y', strtotime ( $row->date_expenses ) )
			);
		}
		$events = new Admin_Model_Event ();
		$rowss = $events->fetchAll ( $events->select () );
		$results = array ();
		foreach ( $rowss as $row ) {
			$results [] = array (
					'id' => $row->id,
					'name' => $row->name
			);
		}
		$this->view->Events = $results;
		$this->view->Items = $result;
	}
	
	/**
	 * get Expenses info by ID
	 * 
	 */
	public  function getexpenseinfoAction(){
		$expenses_id = $this->_request->getParam ( 'expenses_id' );
		$result = array ();
		if (isset ( $expenses_id )) {
			
			$table = new Model_Expenses();
			$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
			$select->setIntegrityCheck ( false )->where ( 'id = ?', $expenses_id );
			$rows = $table->fetchAll ( $select );
			$result = array ();
			foreach ( $rows as $row ) {
				$result [] = array (
						'id' => $row->id,
						'event_id' => $row->event_id,
						'user_id' => $row->user_id,
						'expenses_type_id' => $row->expenses_type_id,
						'check_out_id' => $row->check_out_id,
						'date_expenses' => date ( 'd-m-Y', strtotime ( $row->date_expenses ) )  ,
						'description' => $row->description,
						'settled' => $row->settled,
						'date_settled' =>date ( 'd-m-Y', strtotime ( $row->date_settled ) )  ,
						'amount' => $row->amount
				);
			}
			
			
		} else {
			$result ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $result );
		die ();
		
	} 
	
	public function getlistbyuserAction() {
		$user_id = $this->_request->getParam ( 'user_id' );
		$result = array ();
		if (isset ( $user_id )) {
			$table = new Model_Expenses (); // user_pieces là class extends
			                                // Zend_Db_Table_Abstract
			$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
			$select->setIntegrityCheck ( false )->join ( 'event', 'event.id = expenses.event_id', array (
					'*',
					'expenses.description as expenses_des',
					' IFNULL((select count(ue.user_id) from participants ue where ue.expenses_id = expenses.id),0) as  totalpeople' 
			) )->join ( 'users', 'expenses.user_id = users.id', array (
					'users.name as user_name',
					'expenses.id as expenses_id' 
			) )->where ( 'expenses.user_id = ?', $user_id );
			
			$rows = $table->fetchAll ( $select );
			
			
			foreach ( $rows as $row ) {
				
				$result [] = array (
						'id' => $row->expenses_id,
						'eventname' => $row->name,
						'description' => $row->expenses_des,
						'event_id' => $row->event_id,
						'user_name' => $row->user_name,
						'totalamount' => $row->amount,
						'totalpeople' => $row->totalpeople,
						'settled' => $row->settled,
						'name_paidby' => $row->user_name,
						'expenses_type_id' => $row->expenses_type_id,
						'created' => date ( 'd-m-Y', strtotime ( $row->date_expenses ) ) 
				);
			}
		} else {
			$result ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $result );
		die ();
	}
	/*
	 * param: event_id return : objects expenses
	 */
	public function getlistbyeventAction() {
		$event_id = $this->_request->getParam ( 'event_id' );
		$result = array ();
		if (isset ( $event_id )) {
			
			$table = new Model_Expenses (); // user_pieces là class extends
			                                // Zend_Db_Table_Abstract
			$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
			$select->setIntegrityCheck ( false )->join ( 'event', 'event.id = expenses.event_id', array (
					'*',
					'expenses.description as expenses_des',
					' IFNULL((select count(ue.user_id) from participants ue where ue.expenses_id = expenses.id),0) as  totalpeople' 
			) )->join ( 'users', 'expenses.user_id = users.id', array (
					'users.name as user_name',
					'expenses.id as expenses_id' 
			) )->where ( 'expenses.event_id = ?', $event_id );
			// echo $select->__toString();exit;
			$rows = $table->fetchAll ( $select );
			
			
			foreach ( $rows as $row ) {
				
				$result [] = array (
						'id' => $row->expenses_id,
						'eventname' => $row->name,
						'description' => $row->expenses_des,
						'event_id' => $row->event_id,
						'user_name' => $row->user_name,
						'totalamount' => $row->amount,
						'totalpeople' => $row->totalpeople,
						'settled' => $row->settled,
						'name_paidby' => $row->user_name,
						'expenses_type_id' => $row->expenses_type_id,
						'created' => date ( 'd-m-Y', strtotime ( $row->date_expenses ) ) 
				);
			}
		} else {
			$result ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $result );
		die ();
	}
	/**
	 * Add entry {Json}
	 */
	public function addAction() {
		$result = array ();
		$expenses = new Model_Expenses ();
		$paid_id = $this->_request->getParam ( 'paid_id' );
		$user_id = $this->_request->getParam ( 'user_id' );
		if (isset ( $paid_id ) && isset ( $user_id )) {
			
			$expenses_type_id = $this->_request->getParam ( 'expenses_type_id', 0 );
			$amount = $this->_request->getParam ( 'amount' );
			$description = $this->_request->getParam ( 'description' );
			$settled = ( int ) $this->_request->getParam ( 'settled', 0 );
			$event_id = $this->_request->getParam ( 'event_id' );
			$date_settled = $this->_request->getParam ( 'date_settled' );
			$participantsstr = $this->_request->getParam ( 'member' ); // 1,8#5,9
			$participants = array ();
			$participantsarr = explode ( '-', $participantsstr );
			
			foreach ( $participantsarr as $key => $value ) {
				if ($value != '') {
					$idval = explode ( ',', $value );
					$participants [$idval [0]] = $idval [1];
				}
			}
			
			$id = $this->_request->getParam ( 'id' );
			$data = array (
					'event_id' => $event_id,
					'expenses_type_id' => $expenses_type_id,
					'amount' => $amount,
					'description' => $description,
					'settled' => $settled,
					'date_settled' => $date_settled,
					'user_id' => $paid_id 
			);
			
			$result = $expenses->saveExpenses ( $data, $participants, $id, $user_id );
		} else {
			$result ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $result );
		die ();
	}
	public function removeAction() {
		$events = new Model_Event ();
		$id = $this->_request->getParam ( 'id' );
		$return = $events->delete ( 'id=' . $id );
		echo $return;
	}
}
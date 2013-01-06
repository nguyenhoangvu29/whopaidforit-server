<?php
class ExpensesController extends Zend_Controller_Action {
	
	public function init() {
		$this->_helper->layout->disableLayout();
	}
	
	public function indexAction() {
		$events = new Default_Model_Expenses ();
		$rows = $events->fetchAll ( $events->select () );
		$result = array ();
		foreach ( $rows as $row ) {
			$result [] = array (
					'id' => $row->id,
					'name' => $row->name,
					'created' => date ( 'd-m-Y', strtotime ( $row->date_expenses ) ) 
			);
		}
		echo json_encode ( $result );
		exit;
	}
	
	/**
	 * get Expenses info by ID
	 * 
	 */
	public function getexpenseinfoAction(){
		$expenses_id = $this->_request->getParam ( 'expenses_id' );
		$result = array ();
		if (isset ( $expenses_id )) {
			
			$table = new Default_Model_Expenses();
			$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
			$select->setIntegrityCheck ( false )
					->join('users','expenses.user_id = users.id',array('users.name as user_name'))
					->where ( 'expenses.id = ?', $expenses_id );
			//echo $select->__toString();die;
			$rows = $table->fetchAll ( $select );
			$result = array ();
			foreach ( $rows as $row ) {
				
				//get paticipant
				$tabpar = new Default_Model_Participant();
				$selectpar = $tabpar->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
				$selectpar->setIntegrityCheck ( false )
						->join('users','participants.user_id = users.id',array('users.name as username'))
						->where ( 'expenses_id = ?', $row->id );
				//echo $selectpar->__toString();die;
				$arrpar = $tabpar->fetchAll ( $selectpar );
				$arr = array();
				
				//sum quantity
				$totalquan =0;
				foreach ( $arrpar as $rows ) {
					$totalquan += $rows->quantity;
				}
				foreach ( $arrpar as $rows ) {
					$arrr = array(
							'user_id' =>$rows->user_id,
							'participant_id' => $rows->id,
							'userName'  => $rows->username,
							'quantity' =>$rows->quantity,
							'total' => $totalquan == 0? 0 :($row->amount / $totalquan ) * $rows->quantity
							);
					$arr[]=$arrr;
				}
				
				$result = array (
						'id' => $row->id,
						'event_id' => $row->event_id,
						'user_id' => $row->user_id,
						'owner_id' => $row->owner_id,
						'user_name' => $row->user_name,
						'expenses_type_id' => $row->expenses_type_id,
						'check_out_id' => $row->check_out_id,
						'date_expenses' => date ( 'd-m-Y', strtotime ( $row->date_expenses ) )  ,
						'description' => $row->description,
						'settled' => $row->settled,
						'date_settled' =>date ( 'd-m-Y', strtotime ( $row->date_settled ) )  ,
						'amount' => $row->amount,
						'total' => $totalquan,
						'participants' =>$arr
						
				);
			}
			
			
		} else {
			$result ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $result );
		die ();
		
	} 
	
	public function getparticipantsAction(){
		$expenses_id = $this->_request->getParam ( 'expenses_id' );
		$result = array ();
		if (isset ( $expenses_id )) {
				//get paticipant
				$tabpar = new Default_Model_Participant();
				$selectpar = $tabpar->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
				$selectpar->setIntegrityCheck ( false )
						->join('users','participants.user_id = users.id',array('users.name as user_name'))
						->where ( 'participants.expenses_id = ?', $expenses_id );
				//echo $selectpar->__toString();die;
				$arrpar = $tabpar->fetchAll ( $selectpar );
				
				foreach ( $arrpar as $rows ) {
					$arrr = array(
							'user_id' =>$rows->user_id,
							'participant_id' => $rows->id,
							'quantity' =>$rows->quantity,
							'user_name'=>$rows->user_name
					);
					$result[]=$arrr;
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
			$table = new Default_Model_Expenses (); // user_pieces là class extends
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
			
			$table = new Default_Model_Expenses (); // user_pieces là class extends
			                                // Zend_Db_Table_Abstract
			$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
			$select->setIntegrityCheck ( false )->join ( 'event', 'event.id = expenses.event_id', array (
					'*',
					'expenses.description as expenses_des',
					' IFNULL((select count(ue.user_id) from participants ue where ue.expenses_id = expenses.id),0) as  totalpeople' 
			) )->join ( 'users', 'expenses.user_id = users.id', array (
					'users.name as user_name',
					'expenses.id as expenses_id' 
			) )->where ( 'settled = 0 and expenses.event_id = ?', $event_id );
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
		$expenses = new Default_Model_Expenses ();
		$paid_id = $this->_request->getParam ( 'paid_id' );
		$user_id = $this->_request->getParam ( 'user_id' );
		if (isset ( $paid_id ) && isset ( $user_id )) {
			
			$expenses_type_id = $this->_request->getParam ( 'expenses_type_id', 0 );
			$amount = $this->_request->getParam ( 'amount' );
			$description = $this->_request->getParam ( 'description' );
			$settled = ( int ) $this->_request->getParam ( 'settled', 0 );
			$event_id = $this->_request->getParam ( 'event_id' );
			$date_expenses = $this->_request->getParam ( 'date' );
			$participantsstr = $this->_request->getParam ( 'member' ); // 1,8#5,9
			$participants = array ();
			$participantsarr = explode ( '-', $participantsstr );
			
			$arr = explode('-',$date_expenses);
			if(strlen($arr[0]) != 4){
				$date_expenses = $arr[2].'-'.$arr[1].'-'.$arr[0];
			}
			
			
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
					'date_expenses' => $date_expenses,
					'user_id' => $paid_id,
					'owner_id' => $user_id 
			);
			
			$result = $expenses->saveExpenses ( $data, $participants, $id, $user_id );
		} else {
			$result ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $result );
		die ();
	}
	public function removeAction() {
		$events = new Default_Model_Expenses();
		$id = $this->_request->getParam ( 'id' );
		$db = Zend_Db_Table::getDefaultAdapter();
		$db->delete('participants',"expenses_id = $id");
		$return = $events->delete ( 'id=' . $id );
		echo $return;
	}
}
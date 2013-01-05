<?php
class Admin_AdminCheckoutController extends ZendSlicehtml_Controller_Action {
	
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
		$checkout = new Admin_Model_Checkout();
		$select = $checkout->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
		$select->setIntegrityCheck ( false )
		->join('users as us','us.id = checkout.user_id',array('us.name as user_name') )
		->join('event as ev',' ev.id = checkout.event_id', array('ev.name as event_name'));
		//$select->where( 'users.id = ?', $user_id );
		// print_r($select->__toString());die;
		$rows = $checkout->fetchAll ( $select );
		
		$result = array ();
		foreach ( $rows as $row ) {
			$result [] = array (
					'id' => $row->id,
					'user_name' => $row->user_name,
					'event_name' => $row->event_name,
					'date' => date ( 'd-m-Y', strtotime ( $row->date ) )
			);
		}
		
		$this->view->Items = $result;
	}
	
	public function viewAction() {
		
		$checkout_id = $this->_request->getParam ( 'id' );
		$arr = array ();
		if (isset ( $checkout_id )) {
				
			$arr = $this->getCheckoutInfo ( $checkout_id );
			
			//get info checkout
			$checkout = new Admin_Model_Checkout();
			$select = $checkout->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
			$select->setIntegrityCheck ( false )
			->join('users as us','us.id = checkout.user_id',array('us.name as user_name') )
			->join('event as ev',' ev.id = checkout.event_id', array('ev.name as event_name'));
			$select->where( 'checkout.id = ?', $checkout_id );
			//print_r($select->__toString());die;
			$rows = $checkout->fetchAll ( $select )->current();
			$this->view->Checkout = $rows->toArray();
			
		} else {
			$arr ['error'] = 'Error! Missing data requirements.';
		}
		
		
		
		$this->view->Items = $arr;
	}
	
	
	/**
	 * get all info by event_id
	 *
	 * @param
	 *        	: event_id
	 */
	public function getinfocheckoutAction() {
		$event_id = $this->_request->getParam ( 'event_id' );
		$arr = array ();
		if (isset ( $event_id )) {
			$arr = $this->getData ( $event_id );
		} else {
			$arr ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $arr );
		die ();
	}
	
	/**
	 * get detail of checkout by id checkout
	 *
	 * @param
	 *        	: checkout_id
	 */
	public function getchekoutinfoAction() {
		$checkout_id = $this->_request->getParam ( 'checkout_id' );
		$arr = array ();
		if (isset ( $checkout_id )) {
			
			$arr = $this->getCheckoutInfo ( $checkout_id );
		} else {
			$arr ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $arr );
		die ();
	}
	
	/**
	 * get all checkout history of event
	 *
	 * @param
	 *        	: event_id
	 */
	public function getlistchekoutAction() {
		$event_id = $this->_request->getParam ( 'event_id' );
		$result = array ();
		if (isset ( $event_id )) {
			$checkout = new Model_Checkout ();
			$select = $checkout->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
			$select->setIntegrityCheck ( false )->join ( 'users', 'users.id = checkout.user_id', array (
					'users.name as username' 
			) )->where ( 'event_id = ?', $event_id );
			
			$rows = $checkout->fetchAll ( $select );
			
			foreach ( $rows as $row ) {
				$val = array (
						'id' => $row->id,
						'date' => date ( 'd-m-Y', strtotime ( $row->date ) ),
						'user_id' => $row->user_id,
						'username' => $row->username,
						'event_id' => $row->event_id 
				);
				
				$result [] = $val;
			}
		} else {
			$result ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $result );
		die ();
	}
	
	/**
	 *
	 *
	 * get checkout detail by checkout_id
	 * 
	 * @param int $checkoutid        	
	 * @return json
	 */
	public function getCheckoutInfo($checkoutid) {
		// get all expenses id in checkout id
		$checkout = new Admin_Model_Checkout ();
		$select = $checkout->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
		$select->setIntegrityCheck ( false )->join ( 'checkout_expenses as ce', ' ce.checkout_id = checkout.id' )->where ( 'checkout.id =?', $checkoutid );
		//print_r($select->__toString());die;
		$rows = $checkout->fetchAll ( $select );
		
		$stringid = '';
		foreach ( $rows as $row ) {
			$stringid .= $row->expenses_id . ',';
		}
		$stringid = substr_replace ( $stringid, "", - 1 );
		
		$expenses = new Admin_Model_Expenses ();
		$selects = $expenses->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array (
				' expenses.id' 
		) );
		$selects->setIntegrityCheck ( false )->join ( 'users as us', 'us.id = expenses.user_id', array (
				'us.name as user_create_name,
				us.name as temp ,
				(select name  from users where pa.user_id=users.id ) as member_name',
				'(round( (expenses.amount / (select sum(par.quantity) from participants par where expenses.id = par.expenses_id ) ),2) * pa.quantity ) as amount_member' 
		) )->join ( 'participants as pa', 'pa.expenses_id = expenses.id', array (
				'pa.quantity',
				'pa.user_id as member_id' 
		) )->where ( 'expenses.id in ( ' . $stringid . ')' );
		
		// print_r($select->__toString()); die; //print query
		
		$rows = $expenses->fetchAll ( $selects );
		
		$result = array ();
		$data = array ();
		
		foreach ( $rows as $row ) {
			$result [$row->user_id] = $row;
		}
		foreach ( $rows as $row ) {
			$name = $row->member_name;
			$row->temp = $name;
			$result [$row->member_id] = $row;
		}
		// foreach all memmber in event
		foreach ( $result as $key => $value ) {
			$content = array ();
			$content ['user'] ['user_id'] = $key;
			$content ['user'] ['user_name'] = $value->temp;
			$sub = array ();
			
			$totalamount = 0;
			
			// get all receives of user
			$receives = $this->groupby ( $rows, 'id', $key, 'user_id' );
			// get all pay of user
			$pays = $this->groupby ( $rows, 'id', $key, 'member_id' );
			
			foreach ( $receives as $keyreceive => $receive ) {
				// by expenses id
				$rowset = array ();
				
				foreach ( $receive as $keys => $rec ) {
					
					$amounts = $rec->amount_member;
					$totalamount += $amounts;
					$rowset ['amount'] = $amounts;
					$rowset ['status'] = 0;
					$rowset ['user_refer'] = $rec->member_name;
					$rowset ['user_id_refer'] = $rec->member_id;
					$sub [] = $rowset;
				}
			}
			
			foreach ( $pays as $keypay => $pay ) {
				$rowset = array ();
				foreach ( $pay as $keys => $pa ) {
					$amounts = - $pa->amount_member;
					$totalamount += $amounts;
					$rowset ['amount'] = $amounts;
					$rowset ['status'] = 1;
					$rowset ['user_refer'] = $pa->user_create_name;
					$rowset ['user_id_refer'] = $pa->member_id;
					$sub [] = $rowset;
				}
			}
			
			$content ['amount'] = $totalamount;
			
			// group by sub before fill in content
			$sub = $this->groupbysub ( $sub, 'user_refer' );
			
			$content ['detail'] = $sub;
			$data [] = $content;
		}
		return $data;
	}
	public function getData($event_id) {
		$result = array ();
		$user = new Model_User ();
		$expenses = new Model_Expenses ();
		$select = $expenses->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array (
				' expenses.id' 
		) );
		$select->setIntegrityCheck ( false )->join ( 'users as us', 'us.id = expenses.user_id', array (
				'us.name as user_create_name,
				us.name as temp ,
				(select name  from users where pa.user_id=users.id ) as member_name',
				'(round( (expenses.amount / (select sum(par.quantity) from participants par where expenses.id = par.expenses_id ) ),2) * pa.quantity ) as amount_member' 
		) )->join ( 'participants as pa', 'pa.expenses_id = expenses.id', array (
				'pa.quantity',
				'pa.user_id as member_id' 
		) )->where ( 'expenses.settled = 0 and expenses.event_id = ' . $event_id );
		
		// print_r($select->__toString()); die; //print query
		
		$rows = $expenses->fetchAll ( $select );
		
		$result = array ();
		$data = array ();
		
		foreach ( $rows as $row ) {
			$result [$row->user_id] = $row;
		}
		foreach ( $rows as $row ) {
			$name = $row->member_name;
			$row->temp = $name;
			$result [$row->member_id] = $row;
		}
		// foreach all memmber in event
		foreach ( $result as $key => $value ) {
			$content = array ();
			$content ['user'] ['user_id'] = $key;
			$content ['user'] ['user_name'] = $value->temp;
			$sub = array ();
			
			$totalamount = 0;
			
			// get all receives of user
			$receives = $this->groupby ( $rows, 'id', $key, 'user_id' );
			// get all pay of user
			$pays = $this->groupby ( $rows, 'id', $key, 'member_id' );
			
			foreach ( $receives as $keyreceive => $receive ) {
				// by expenses id
				$rowset = array ();
				
				foreach ( $receive as $keys => $rec ) {
					
					$amounts = $rec->amount_member;
					$totalamount += $amounts;
					$rowset ['amount'] = $amounts;
					$rowset ['status'] = 0;
					$rowset ['user_refer'] = $rec->member_name;
					$rowset ['user_id_refer'] = $rec->member_id;
					$sub [] = $rowset;
				}
			}
			
			foreach ( $pays as $keypay => $pay ) {
				$rowset = array ();
				foreach ( $pay as $keys => $pa ) {
					$amounts = - $pa->amount_member;
					$totalamount += $amounts;
					$rowset ['amount'] = $amounts;
					$rowset ['status'] = 1;
					$rowset ['user_refer'] = $pa->user_create_name;
					$rowset ['user_id_refer'] = $pa->member_id;
					$sub [] = $rowset;
				}
			}
			
			$content ['amount'] = $totalamount;
			
			// group by sub before fill in content
			$sub = $this->groupbysub ( $sub, 'user_refer' );
			
			$content ['detail'] = $sub;
			$data [] = $content;
		}
		return $data;
	}
	/**
	 * Checkout for event
	 */
	public function addinfocheckoutAction() {
		$event_id = $this->_request->getParam ( 'event_id' );
		$user_id = $this->_request->getParam ( 'user_id' );
		
		$result = array ();
		if (isset ( $event_id ) && isset ( $user_id )) {
			
			$datas = $this->getData ( $event_id );
			// update all set settled = 1
			
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
			) )->where ( 'expenses.event_id = ?', $event_id )->where ( 'expenses.settled = 0' );
			$rows = $table->fetchAll ( $select );
			$stringid = '';
			
			if ($rows->count () > 0) {
				
				foreach ( $rows as $row ) {
					
					// get list id
					$stringid .= $row->expenses_id . ',';
				}
				
				$arrupdate = array (
						'settled' => 1 
				);
				$stringid = substr_replace ( $stringid, "", - 1 );
				// print_r ( $rows->count () );die ();
				
				$update = new Zend_Db_Table ( array (
						'name' => 'expenses' 
				) );
				$update->update ( $arrupdate, 'id in (' . $stringid . ')' );
				
				// step 1: Save in table checkout
				
				$arrcheckout = array (
						'date' => date ( 'Y-m-d h:i:s' ),
						'user_id' => $user_id,
						'event_id' => $event_id 
				);
				$dblog = new Zend_Db_Table ( array (
						'name' => 'checkout' 
				) );
				
				$idchecouttbl = $dblog->insert ( $arrcheckout );
				
				// step 2: Save in table Expenses
				foreach ( $datas as $key => $data ) {
					
					$user_id_re = $data ['user'] ['user_id'];
					
					foreach ( $data ['detail'] as $k => $detail ) {
						
						if ($detail ['status'] == 1) { // Pay
							$result ['detail'] [] = $user_id_re . ' -> ' . $detail ['user_id_refer'] . ' : ' . - $detail ['amount'];
							$arrexpenses = array (
									'event_id' => $event_id,
									'expenses_type_id' => 2,
									'amount' => - $detail ['amount'],
									'description' => 'Checkout',
									'check_out_id' => $idchecouttbl,
									'settled' => 1,
									'date_settled' => date ( 'Y-m-d h:i:s' ),
									'user_id' => $user_id_re 
							);
							
							$dbex = new Zend_Db_Table ( array (
									'name' => 'expenses' 
							) );
							$idextbl = $dbex->insert ( $arrexpenses );
							
							// step 3: Save in table Participants
							$dataspar = array (
									'quantity' => 1,
									'user_id' => $detail ['user_id_refer'],
									'expenses_id' => $idextbl,
									'date_created' => date ( 'Y-m-d h:i:s' ),
									'date_deleted' => date ( 'Y-m-d h:i:s' ),
									'created_by' => $user_id 
							);
							$dbpar = new Zend_Db_Table ( array (
									'name' => 'participants' 
							) );
							$idpar = $dbpar->insert ( $dataspar );
							
							// step 4: Save in table checkout_expenses
							$data_ex_check = array (
									'checkout_id' => $idchecouttbl,
									'expenses_id' => $idextbl 
							);
							$dbpar = new Zend_Db_Table ( array (
									'name' => 'checkout_expenses' 
							) );
							$idex_check = $dbpar->insert ( $data_ex_check );
						}
					}
				}
				$result ['success'] = 'Success';
			} else {
				$result ['message'] = 'Have no record in expenses';
			}
		} else {
			$result ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $result );
		die ();
	}
	
	/**
	 * Description: Group detail array by user_id_refer and sum mount
	 *
	 * @param array $model        	
	 * @param string $field        	
	 * @return array
	 */
	public function groupbysub($model, $field) {
		$arr = array ();
		$return = array ();
		foreach ( $model as $key => $data ) {
			
			$arr [$data ['user_id_refer']] [] = $data;
		}
		foreach ( $arr as $key => $data ) {
			$amounts = 0;
			foreach ( $data as $key => $da ) {
				$amounts += $da ['amount'];
			}
			$rowset = array ();
			$rowset ['amount'] = $amounts;
			$rowset ['status'] = $data [0] ['status'];
			$rowset ['user_refer'] = $data [0] [$field];
			$rowset ['user_id_refer'] = $data [0] ['user_id_refer'];
			$return [] = $rowset;
		}
		return $return;
	}
	public function groupby($rows, $col, $if, $ifcom) {
		$result = array ();
		foreach ( $rows as $row ) {
			
			/*
			 * if($if == $row->member_id){ continue; }
			 */
			if ($ifcom == 'member_id') {
				if ($if == $row->$ifcom) {
					$row->member_id = $row->user_id;
					if ($row->member_id != $if) {
						$result [$row->$col] [] = $row;
					}
				}
			} else {
				if ($if == $row->$ifcom && $if != $row->member_id) {
					$result [$row->$col] [] = $row;
				}
			}
		}
		return $result;
	}
}
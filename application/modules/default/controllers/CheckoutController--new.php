<?php
class CheckoutController extends Zend_Controller_Action {
	
	
	public function init() {
		
		$this->_helper->layout->disableLayout();
	}
	/**
	 * get all info by event_id
	 *
	 * @param
	 *        	: event_id
	 */
	 public function indexAction(){
		echo 12;die;
	 }
	public function getinfocheckoutAction() {
		
		$event_id = $this->_request->getParam ( 'event_id' );
		$arr = array ();
echo 1;exit;
		if (isset ( $event_id )) {
			$arr = $this->getData ( $event_id );
		} else {
			$arr ['error'] = 'Error! Missing data requirements.';
		}
		echo json_encode ( $arr );
		die ();
	}
	public function updatestatusparAction() {
	
		$par_id = $this->_request->getParam ( 'par_id' );
		$ispay = $this->_request->getParam ( 'ispay' );
		$arr = array ();
	
		if (isset ( $par_id ) &&  isset ( $ispay )) {
	

			$arrupdate = array (
			
					'ispay' => ($ispay ==0 ? 1: 0)
			
			);
			
			$update = new Zend_Db_Table ( array (
			
					'name' => 'participants'
			
			) );
			
			$result = $update->update ( $arrupdate, 'id =' . $par_id );	
			arr['status'] = $result;
	
		} else {
			arr['status'] = 0;
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
			echo 1;exit;
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
			$checkout = new Default_Model_Checkout ();
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
		$checkout = new Default_Model_Checkout ();
		$select = $checkout->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
		$select->setIntegrityCheck ( false )
			->join ( 'checkout_expenses as ce', ' ce.checkout_id = checkout.id', array() )
			->join ( 'expenses as e', ' e.id = ce.expenses_id',array('e.user_id','e.amount') )
			->join ( 'users as u', ' u.id = e.user_id',array('u.name as user_name') )
			->join ( 'participants as p', ' p.expenses_id = e.id',array('p.quantity','p.status','p.amount as member_amount','p.user_id as member_id', 'p.id as participant_id','p.ispaid') )
			->join ( 'users as m', ' m.id = p.user_id',array('m.name as member_name') )
			->where ( 'checkout.id =?', $checkoutid );
		// print_r($select->__toString());die;
		$rows = $checkout->fetchAll ( $select );
		$result = array ();
		
		foreach ( $rows as $row ) {
			$result [] = array('id'=>$row->id,
					'user_id' => $row->user_id,
					'user_name'=> $row->user_name,
					'member_name' => $row->member_name,
					'amount' => $row->amount,
					'quantity' => $row->quantity,
					'member_id' => $row->member_id,
					'participant_id' => $row->participant_id,
					'member_amount' => $row->member_amount,
					'status' => $row->status,
					'ispaid' => $row->ispaid);
		}
		return $result;
	}
	public function getData($event_id) {
		$result = array ();
		
		$user = new Default_Model_User ();
		$expenses = new Default_Model_Expenses ();
		$select = $expenses->select ( array('expenses.id'), array (
				' expenses.id'
		) );
		
		$select->setIntegrityCheck ( false )->join ( 'users as us', 'us.id = expenses.user_id', array (
				'us.name as user_create_name,
				(select name  from users where pa.user_id=users.id ) as member_name',
				'sum(round( (expenses.amount / (select sum(par.quantity) from participants par where expenses.id = par.expenses_id ) ),2) * pa.quantity ) as amount_member'
		) )->join ( 'participants as pa', 'pa.expenses_id = expenses.id', array (
				'pa.quantity',
				'pa.user_id as member_id',
				'pa.ispaid as ispay' ,
				'pa.id as participant_id'
		) )->where ( 'expenses.settled = 0 and expenses.event_id = ' . $event_id." and pa.user_id<>`expenses`.user_id" )
		->group(array("expenses.user_id",'pa.user_id'));
		//echo $select->__toString();
		
		$rows = $expenses->fetchAll ( $select );
		
		foreach ( $rows as $row ) {
			$result [] = array('id'=>$row->id,
								'user_id' => $row->user_id,
								'user_name'=> $row->user_create_name,
								'member_name' => $row->member_name,
								'amount' => $row->amount_member,
								'quantity' => $row->quantity,
								'participant_id' => $row->participant_id,
								'ispay' => $row->ispay,
								'member_id' => $row->member_id);
		}
		
		return $result;
	}
	/**
	 * Checkout for event
	 */
	public function addinfocheckoutAction() {
		$event_id = $this->_request->getParam ( 'event_id' );
		$user_id = $this->_request->getParam ( 'user_id' );
	
		$result = array ();
		$result['checkout_id'] = 0;
		if (isset ( $event_id ) && isset ( $user_id )) {
			$datas = $this->getData ( $event_id );
			$userId = 0;
			$number = 0;
			$i=0;
			$items = array();
			foreach($datas as $data){
				$i++;
				if($userId != $data['user_id']){
					$userId = $data['user_id'];
					$result[$number] = array('user_id'=>$data['user_id'],'user_name'=>$data['user_name'],'amount'=>$data['amount'], 'status'=>1);
					$number++;
				}else{
					$result[$number-1] = array('user_id'=>$data['user_id'],'user_name'=>$data['user_name'],'amount'=>$data['amount'] + $result[$number-1]['amount'],'status'=>1);
				}
				$item = array('member_id'=> $data['member_id'],'member_name'=>$data['member_name'],'amount'=>$data['amount'],'status'=>1);
				$items[$number-1][$data['member_id']] = $item;
			}
			//get paid
			foreach($datas as $data){
				$flag = true;
				$member_id = $data['member_id'];
				for($t = 0;$t<count($result);$t++){
					if($member_id == $result[$t]['user_id']){
						$flag = false;
						$result[$t]['amount'] -= $data['amount'];
						$status = 1;
						if($result[$t]['amount'] <0) $status = 0;
						$result[$t]['status'] = $status;
						$item = array('member_id'=> $data['user_id'],'member_name'=>$data['user_name'],'amount'=>$data['amount'],'status'=>0);
						$items[$t][$data['member_id']] = $item; 
					}
				}
				if($flag){
					$amount = $data['amount'];
					$amount = -$amount;
					$status = 1;
					if($amount<0) $status = 0;
					$result[$number] = array('user_id'=>$data['member_id'],'user_name'=>$data['member_name'],'amount'=>$amount, 'status'=>$status);
					$item = array('member_id'=> $data['user_id'],'member_name'=>$data['user_name'],'amount'=>$data['amount'],'status'=>0);
					$items[$number][$data['user_id']] = $item;
					$number++;
				}
			}
			
		}
		//group 
		for($i=0;$i<count($items);$i++){
			$subs = $items[$i];
			$new_items = array();
			foreach($subs as $key=>$value){
				$new_items[] = $value;
			}
			$items[$i] = $new_items;
			for($t = 0;$t<count($items[$i]);$t++){
				for($n = $t+1;$n<count($items[$i]);$n++){
					if($items[$i][$n]['member_id'] == $items[$i][$t]['member_id']){
						$total = 0;
						if($items[$i][$t]['status'] > 0)
							$total += $items[$i][$t]['amount'];
						else 
							$total -= $items[$i][$t]['amount'];
						if($items[$i][$n]['status'] > 0)
							$total += $items[$i][$n]['amount'];
						else
							$total -= $items[$i][$n]['amount'];
						
						$items[$i][$t]['amount'] = $total;
						if($total > 0)
							$items[$i][$t]['status'] = 1;
						else{
							$items[$i][$t]['status'] = 0;
							$total = -$total;
						}
						unset($items[$i][$n]);
					}
				}
			}
		}
		//update db
		$arrupdate = array (
				'settled' => 1,'date_settled'=> date('Y-m-d h:i:s') 
		);
		$update = new Zend_Db_Table ( array (
				'name' => 'expenses' 
		) );
		$update->update ( $arrupdate, 'event_id =' . $event_id );
		
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
		$result['checkout_id'] = $idchecouttbl;
		//save expenses
		for($i=0;$i<count($result);$i++){
			$arrexpenses = array (
					'event_id' => $event_id,
					'expenses_type_id' => 2,
					'amount' => $result[$i]['amount'],
					'description' => 'Checkout',
					'check_out_id' => $idchecouttbl,
					'settled' => 1,
					'date_settled' => date ( 'Y-m-d h:i:s' ),
					'user_id' => $result[$i]['user_id']
			);
				
			$dbex = new Zend_Db_Table ( array (
					'name' => 'expenses'
			) );
			$idextbl = $dbex->insert ( $arrexpenses );
				
			// step 3: Save in table Participants
			for($t=0;$t<count($items[$i]);$t++){
				$dataspar = array (
						'quantity' => 1,
						'amount'=> $items[$i][$t]['amount'],
						'status'=> $items[$i][$t]['status'],
						'user_id' => $items[$i][$t]['member_id'],
						'expenses_id' => $idextbl,
						'date_created' => date ( 'Y-m-d h:i:s' ),
						'date_deleted' => date ( 'Y-m-d h:i:s' ),
						'created_by' => $user_id
				);
				$dbpar = new Zend_Db_Table ( array (
						'name' => 'participants'
				) );
				$idpar = $dbpar->insert ( $dataspar );
			}
			
				
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
		echo json_encode ( $result );
		die();
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
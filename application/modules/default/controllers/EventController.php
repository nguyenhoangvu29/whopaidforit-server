<?php
class EventController extends Zend_Controller_Action {
	
	public function init() {
		$this->_helper->layout->disableLayout();
	}
	
	public function indexAction() {
		$events = new Default_Model_Event ();
		$rows = $events->fetchAll ( $events->select () );
		$result = array ();
		foreach ( $rows as $row ) {
			$result [] = array (
					'id' => $row->id,
					'name' => $row->name,
					'created' => date ( 'd-m-Y', strtotime ( $row->created ) ) 
			);
		}
		echo json_encode ( $result );
	}
	public function geteventbyidAction() {
		$id = $this->_request->getParam ( 'id' );
		
		$table = new Default_Model_Event ();
		$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
		$select->setIntegrityCheck ( false )->where ( 'id = ?', $id );
		$rows = $table->fetchAll ( $select );
		$result = array ();
		foreach ( $rows as $row ) {
			$result [] = array (
					'id' => $row->id,
					'name' => $row->name,
					'description' => $row->description 
			);
		}
		echo json_encode ( $result );
		die ();
	}
	public function getlistbyuserAction() {
		$user_id = $this->_request->getParam ( 'user_id' );
		
		$table = new Default_Model_Event (); // user_pieces là class extends
		                            // Zend_Db_Table_Abstract
		$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
		$select->setIntegrityCheck ( false )->join ( 'user_event', 'event.id = user_event.event_id', 
					array (
					' IFNULL((select sum(amount) from expenses n where n.event_id = user_event.event_id group by event_id),0) as  totalamount',
					' IFNULL((select count(ue.user_id) from user_event ue where ue.event_id = user_event.event_id),0) as  totalpeople,
					 (select cu.name from currency  cu where cu.id = event.currency ) as curency_name, user_event.active ' 
					) 
				)
				->join ( 'users', 'user_event.user_id = users.id', array () )
				->where ( 'event.publish = 0 and users.id = ?', $user_id );
		// print_r($select->__toString());die;
		$rows = $table->fetchAll ( $select );
		// echo count($rows);exit;
		// print_r($rows);die;
		$result = array ();
		foreach ( $rows as $row ) {
			$result [] = array (
					'id' => $row->id,
					'name' => $row->name,
					'active' => $row->active,
					'owner_id' => $row->owner_id,
					'curency_name' => '',
					'currency' => $row->currency,
					'totalamount' => $row->totalamount,
					'totalpeople' => $row->totalpeople,
					'description' => $row->description,
					'created' => date ( 'd-m-Y', strtotime ( $row->created ) ) ,
					'date_day_month' => date ( 'd-m', strtotime ( $row->created ) )
			);
		}
		echo json_encode ( $result );
		die ();
	}
	public function addAction() {
		$events = new Default_Model_Event ();
		$name = $this->_request->getParam ( 'name' );
		$description = $this->_request->getParam ( 'description' );
		$user_id = $this->_request->getParam ( 'user_id' );
		$currency_id = $this->_request->getParam ( 'currency' );
		if (! $currency_id)
			$currency_id = 1;
		$id = $this->_request->getParam ( 'id' );
		$data = array (
				'name' => $name,
				'user_id' => $user_id,
				'owner_id' => $user_id,
				'description' => $description,
				'currency' => $currency_id,
				'created' => date ( 'Y-m-d h:i:s' ) 
		);
		$result = $events->saveEvent ( $data, $id );
		echo json_encode ( $result );
		die ();
	}
	public function removeAction() {
		$events = new Default_Model_Event ();
		$id = $this->_request->getParam ( 'id' );
		$user_id = $this->_request->getParam ( 'user_id' );
		$data = array('user_id'=>$user_id);
		$return = $events->removeEvent( $data, $id );
		echo json_encode ( $return  );
		die;
	}
}
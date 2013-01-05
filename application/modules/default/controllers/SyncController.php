<?php
class SyncController extends Zend_Controller_Action {
	
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
	}
	
	/**
	 * param: queryString
	 * return: success = 0 or 1
	 * return: date synchronize
	 */
	public function addAction() {
		date_default_timezone_set('UTC');
		$currentDate = date('Y-m-d h:i:s');
		$result = array ();
		$sync = new Default_Model_Sync ();
		$event = new Default_Model_Event ();
		$datas = $this->_request->getParam ( 'data' );
		
		$datas = explode('*',$datas);
		$sync_time = $currentDate;
		$user_id = 1;
		
		foreach($datas as $data ){
			$obj = explode(';',$data);
			//print_r($obj);
			$date = explode(',',$obj[0]);
			$data_time = $date[0].' '.$date[1];
			$sync_table = $obj[2];
			$sync_id = $obj[3];
			$sync_type = $obj[4];
			$sync_value = $obj[5];
			$values = array('data_time' => $data_time,
							'sync_time' => $sync_time,
							'sync_table' => $sync_table,
							'sync_id' => $sync_id,
							'sync_type' => $sync_type);
			
			if($sync_type == 1){ // insert
				$sync->saveSync( $values );
				switch($sync_table){
					case 'event':
						$event_data = array('name' => $sync_value, 'uuid' => $sync_id, 'created' => $currentDate,'user_id'=> $user_id);
						$event->saveEvent($event_data);
					break;
				}
			}else{ // update
				// check sync_time local with server
				$select = $sync->select('sync_time ');
				$select->where("sync_id='".$sync_id."'");
				$rows = $sync->fetchAll($select);
				$sync_time_server = 0;
				foreach($rows as $row){
					$sync_time_server = $row->sync_time;
				}
				if($sync_time_server < $sync_time){ // update record and sync
					$data_update = array('data_time'=> $data_time,'sync_time'=>$sync_time,'sync_type'=>2);
					$sync->update($data_update,"sync_id='$sync_id'");
					switch($sync_table){
						case 'event':
							$data_update = array('name'=> $sync_value);
							$event->update($data_update,"uuid='$sync_id'");
						break;
					}
				}
			}
			
		}
		echo 1;
		die ();
	}
	public function getAction() {
		
		$sync = new Default_Model_Sync();
		$event = new Default_Model_Event();
		$date = $this->_request->getParam ( 'date' );
		if($date){
			$date = explode(',',$date);
			$date = $date[0].' '.$date[1];
		}	
		$select = $sync->select(Zend_Db_Table::SELECT_WITH_FROM_PART, array ());
		$select->setIntegrityCheck ( false );
		$select->join('event','event.uuid = sync.sync_id',array("event.name as event_name") );
		$select->where("sync_time > '$date' or sync_time='0000-00-00 00:00:00'");
		$rows = $sync->fetchAll($select);
		$return = array();
		foreach($rows as $row){
			$return[]  = array('data_time'=>$row->data_time,
								'sync_time'=>$row->sync_time,
								'sync_table' => $row->sync_table,
								'sync_id' => $row->sync_id,
								'sync_type' => $row->sync_type,
								'event_name' => $row->event_name);
		}
		echo json_encode($return);
		die();
	}
}
<?php
class Admin_AdminCurrencyController extends Zend_Controller_Action{

	public function indexAction(){
		
	}
	public function getcurrencyAction(){
		$db = new Zend_Db_Table(array('name' => 'currency'));
		$rows = $db->fetchAll();
		$result = array();
		foreach($rows as $row){
			$result[] = array('id'=> $row->id,
					'name' =>$row->name,
					'symbol' =>$row->symbol,
			);
		}
		
		echo json_encode($result); die;
	}
	

}
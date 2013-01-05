<?php
class Admin_AdminParticipantsController extends ZendSlicehtml_Controller_Action {
	
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
		$table = new Admin_Model_Participant(); // user_pieces là class extends

		$select = $table->select ( Zend_Db_Table::SELECT_WITH_FROM_PART, array () );
		$select->setIntegrityCheck ( false )->join ( 'expenses as ex', 'ex.id = participants.expenses_id', array ('ex.description as des_ex',
				'ex.event_id as event_id'
		) )->join ( 'users', 'ex.user_id = users.id', array (
				'users.name as user_name',
				'ex.id as expenses_id'
		) );
		if(isset ( $user_id )){
			
			$select->where ('ex.user_id = ?', $user_id);
		}
		if(isset ( $event_id )){
				
			$select->where ( 'ex.event_id = ?', $event_id );
			$this->view->assign('event_id', $event_id);
				
		}
		//echo $select->__toString();
		$rows = $table->fetchAll ( $select );
			
		$result = array();
		foreach ( $rows as $row ) {
		
			$result [] = array (
					'expenses_id' => $row->expenses_id,
					'id'=> $row->id,
					'event_id' => $row->event_id,
					'des_ex' => $row->des_ex,
					'quantity' => $row->quantity,
					'user_name' => $row->user_name,
					'created' => date ( 'd-m-Y', strtotime ( $row->date_created ) )
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
	
	
}
<?php
class Default_Model_Expenses extends Zend_Db_Table {
	protected $_name = 'expenses';
	public function getExpenses($id) {
		$id = ( int ) $id;
		$rowset = $this->select ()->where ( 'id = ?', $id );
		$row = $this->fetchAll ( $rowset );
		
		if (! $row) {
			throw new Exception ( "Could not find row $id" );
		}
		return $row;
	}
	public function saveExpenses($data, $participants, $id = 0,$create_user_id) {
		try {
			
			$result = array ();
			$id = ( int ) $id;
			if ($id == 0) {
				try {
					$id = $this->insert ( $data ); // save entry
					                               
					// save expensese log
					$arrlog = array (
							'expenses_id' => $id,
							'changed_by_id' => $create_user_id,
							'created_date' => date ( 'Y-m-d h:i:s' ),
							'log_type_id' => 1  // created
					);
					$dblog = new Zend_Db_Table ( array (
							'name' => 'log_expenses' 
					) );
					$dblog->insert ( $arrlog );
					
					// save paticipants
					$db = new Zend_Db_Table ( array (
							'name' => 'participants' 
					) );
					$parcis = array ();
					foreach ( $participants as $key => $value ) {
						$datas = array (
								'quantity' => $value,
								'user_id' => $key,
								'expenses_id' => $id,
								'date_created' => date ( 'Y-m-d h:i:s' ),
								'date_deleted' => null,
								'created_by' => $create_user_id 
						);
						
						$idp = $db->insert ( $datas );
						// save log participants
						$arrlogpar = array (
								'paticipant_id' => $idp,
								'changed_by_id' => $create_user_id,
								'create_date' => date ( 'Y-m-d h:i:s' ) 
						);
						$dblogpar = new Zend_Db_Table ( array (
								'name' => 'log_participant' 
						) );
						$dblogpar->insert ( $arrlogpar );
						
						$parcis [] = $idp;
					}
				} catch ( Exception $e ) {
					print_r ( $e );
				}
				$result ['id_expenses'] = $id;
				$result ['expenses_id'] = $id;
				$result ['id_paticipants'] = $parcis;
				$result ['success'] = 1;
			} else {
				if ($this->getExpenses ( $id )) {
					$result ['success'] = 1;
					$result ['expenses_id'] = $id;
					$this->update ( $data, 'id = '. $id );
					// save expensese log
					$arrlog = array (
							'expenses_id' => $id,
							'changed_by_id' => $create_user_id,
							'created_date' => date ( 'Y-m-d h:i:s' ),
							'log_type_id' => 2  // created
					);
					$dblog = new Zend_Db_Table ( array (
							'name' => 'log_expenses'
					) );
					$dblog->insert ( $arrlog );
					
					// save paticipants
					$db = new Zend_Db_Table ( array (
							'name' => 'participants'
					) );
					$db->delete('expenses_id='.$id);
					$parcis = array ();
					foreach ( $participants as $key => $value ) {
						$datas = array (
								'quantity' => $value,
								'user_id' => $key,
								'expenses_id' => $id,
								'date_created' => date ( 'Y-m-d h:i:s' ),
								'date_deleted' => null,
								'created_by' => $create_user_id
						);
					
						$idp = $db->insert ( $datas );
						// save log participants
						$arrlogpar = array (
								'paticipant_id' => $idp,
								'changed_by_id' => $create_user_id,
								'create_date' => date ( 'Y-m-d h:i:s' )
						);
						$dblogpar = new Zend_Db_Table ( array (
								'name' => 'log_participant'
						) );
						$dblogpar->insert ( $arrlogpar );
					
						$parcis [] = $idp;
					}
				} else {
					$result ['success'] = 0;
					throw new Exception ( 'Form id does not exist' );
				}
			}
		} catch ( Exception $e ) {
			$result ['message'] = 'Error: ' . $e->getMessage ();
		}
		return $result;
	}
}
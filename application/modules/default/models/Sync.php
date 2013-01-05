<?php
class Default_Model_Sync extends Zend_Db_Table {
	protected $_name = 'sync';
	public function getSync($id) {
		$id = ( int ) $id;
		$rowset = $this->select ()->where ( 'id = ?', $id );
		$row = $this->fetchAll ( $rowset );
		
		if (! $row) {
			throw new Exception ( "Could not find row $id" );
		}
		return $row;
	}
	public function saveSync($data, $id = 0 ) {
		try {
			
			$result = array ();
			$id = ( int ) $id;
			if ($id == 0) {
				try {
					$id = $this->insert ( $data ); // save entry
				} catch ( Exception $e ) {
					print_r ( $e );
				}
				$result ['id'] = $id;
				$result ['success'] = 1;
			} else {
				if ($this->getSync ( $id )) {
					$result ['success'] = 1;
					$this->update ( $data, 'id = '. $id );
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
<?php
class Default_Model_User extends Zend_Db_Table{
	protected $_name = 'users';
	protected $_primary = "id";
	
	public function getUser($id)
	{
		$id  = (int) $id;
		$rowset = $this->select()->where('id = ?',$id);
		$row = $this->fetchAll($rowset);

		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row;
	}
	public function addMember($data, $id)
	{
		$result = array();
		try{
		$datas = array('user_id'=> 0,'event_id'=>$data['event_id']);
		$user_id = $data['user_id'];
		unset($data['user_id']);
		unset($data['event_id']);
		if($id ==0){
			$data['owner_id'] = $user_id;
			$id = $this->insert($data);
			$datas['user_id'] = $id;
			$db = new Zend_Db_Table(array('name' => 'user_event'));
			$idue = $db->insert($datas);
		
			//log user here
			//log user
			$this->log('log_user', 'user_id', $id, $user_id, 1);
		}else{
			if ($this->getUser($id)) {
				$this->update($data, 'id='.$id );
			
				//log event here
				$arrlog = array(
						'user_id' => $id,
						'changed_by_id' => $data['user_id'],
						'create_date' => date('Y-m-d h:i:s'),
						'log_type_id' => 2,  //update
				);
			
				$dblogevent = new Zend_Db_Table(array('name' => 'log_user'));
				$dblogevent->insert($arrlog);
			
			} else {
				$result['message'] = 'Not exsits user';
				throw new Exception('Form id does not exist');
			}
		}
		$result['success'] = 1;
		$result['id_user'] = $id;
		$result['id_user_event'] = $idue;
		}
		catch(Exception $e){
			$result['success'] = 0;
		}
		return $result;
	}
	public function saveUser($data, $id=0)
	{ 
	
		$result = array();
		$id = (int)$id;
		if ($id == 0) {
            $id = $this->insert($data);
            //log user
            $this->log('log_user', 'user_id', $id, $id, 1);
            
            $result['success'] = 1;
            $result['id'] = $id;
        } else {
       
            if ($this->getUser($id)) {
            	$result['success'] = 1;
            	$result['id'] = $id;
                $this->update($data, 'id = '.$id );
                //log user
                $this->log('log_user', 'user_id', $id, $id, 2);
                
            } else {
            	$result['message'] = 'Save user error!';
                throw new Exception('Form id does not exist');
            }
        }
        return $result;
	}
	public function log($table,$field,$idp,$user,$type){
		$arrlog = array (
				$field => $idp,
				'changed_by_id' => $user,
				'create_date' => date ( 'Y-m-d h:i:s' ),
				'log_type_id' => $type  // created
		);
		$dblogpar = new Zend_Db_Table ( array (
				'name' => $table
		) );
		$dblogpar->insert ( $arrlog );
	}
}
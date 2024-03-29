<?php
class Admin_Model_Event extends Zend_Db_Table{
	protected $_name = 'event';
	
	public function getEvent($id)
	{
		$id  = (int) $id;
		$rowset = $this->select()->where('id = ?',$id);
		$row = $this->fetchAll($rowset);
		
		if (!$row) {
			throw new Exception("Could not find row $id");
		}
		return $row;
	}
	public function saveEvent($data, $id=0)
	{
		$id = (int)$id;
		$result = array();
		$datas =array();
		$datas['user_id'] = $data['user_id'];
		
		unset($data['user_id']);
		//unset($data['created']);
		//unset($data['currency']);
		
		if ($id == 0) {
			$id = $this->insert($data);
            //insert user_even
            $datas['event_id'] = $id;
            
            $db = new Zend_Db_Table(array('name' => 'user_event'));
            $db->insert($datas);
            
            //add create to log table user_event 
            $arrlog = array(
            		'user_event_id' => $id,
            		'changed_by_id' => $datas['user_id'],
            		'create_date' => date('Y-m-d h:i:s'),
            		'log_type_id' => 1,  //created
            		);
            $dblog = new Zend_Db_Table(array('name' => 'log_user_event'));
            $dblog->insert($arrlog);
            
            //log event here
            unset($arrlog['user_event_id']);
            $arrlog['event_id'] = $id;
            $dblogevent = new Zend_Db_Table(array('name' => 'log_event'));
            $dblogevent->insert($arrlog);
            
            $result['event_id'] = $id;
            $result['event_name'] = $data['name'];
            $result['success'] = 1;
            
        } else {
            if ($this->getEvent($id)) {
                $this->update($data, 'id='.$id );
                
                
                //log event here
                $arrlog = array(
                		'event_id' => $id,
                		'changed_by_id' => $data['user_id'],
                		'create_date' => date('Y-m-d h:i:s'),
                		'log_type_id' => 2,  //update
                );

                $dblogevent = new Zend_Db_Table(array('name' => 'log_event'));
                $dblogevent->insert($arrlog);
                
            } else {
            	$result['message'] = 'Not exsits event';
                throw new Exception('Form id does not exist');
            }
        }
        return $result;
	}
	
	public function removeEvent($data,$id=0)
	{
		$id = (int)$id;
		$result = array();
		$datas =array();
		$datas['user_id'] = $data['user_id'];
		
		//unset($data['user_id']);
		//unset($data['created']);
		//unset($data['currency']);
		
		if ($id != 0) {
			//$id = $this->insert($data);
            //insert user_even
            $datas['event_id'] = $id;
            $db = new Zend_Db_Table(array('name' => 'event'));
			$arr = array('publish'=>'1');
            $db->update($arr,"id = $id");
            
            //add create to log table event 
            $arrlog = array(
            		'event_id' => $id,
            		'changed_by_id' => $datas['user_id'],
            		'create_date' => date('Y-m-d h:i:s'),
            		'log_type_id' => 3,  //created
            		);
            $dblog = new Zend_Db_Table(array('name' => 'log_event'));
            $dblog->insert($arrlog);
            
          
            
            $result['event_id'] = $id;
            $result['event_name'] = $data['name'];
            $result['success'] = 1;
            
        } else {
            if ($this->getEvent($id)) {
                $this->update($data, 'id='.$id );
            } else {
            	$result['message'] = 'Not exsits event';
                throw new Exception('Form id does not exist');
            }
        }
        return $result;
	}

	
	
}
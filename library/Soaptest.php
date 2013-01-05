<?php
class Soaptest {
	
	
	
	/**
     * Return Hello World
     * @return string
     */
    public function helloworld()
    {
        return 'Hello World!';
    }
    
	/**
     * Returns list of all download in database
     *
     * @return array
     */
   public function getDownloads() 
    {
      //$db = Zend_Registry::get('Zend_Db');    
      
      $items  = new Model_Item();
      
      $datas  = $items->fetchAll($items->select());
      //header('Content-type: text/xml');
	    $return = '<items>';
	    foreach($datas as $index => $post) {
	        $return .= '<item>';
	        $return .= '<id>'.htmlentities($post['id']).'</id>';
	        $return .= '<name>'.htmlentities($post['name']).'</name>';
	        $return .= '<date>'.htmlentities($post['created']).'</date>';
	        $return .= '</item>';
	    }
	    $return .= '</items>';
	   return $return; 
      /*$sql = "SELECT * FROM item";      
      return $db->fetchAll($sql); */     
    }
    
    /**
     * Returns list of all items in database
     *
     * @return array all objects
     */
    public function getItems()
    {
    	return 4;
    	$items  = new Application_Model_DbTable_Item();
    	return 12;
		$data  = $items->fetchAll($items->select());
    	return $data;
    }
	
}
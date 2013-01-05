<?php
class Default_Model_Checkout extends Zend_Db_Table{
	protected $_name = 'checkout';
	
    public function getCheckout(){
        echo '<br>' .  __CLASS__ . ' - ' .__FUNCTION__;
    }
}
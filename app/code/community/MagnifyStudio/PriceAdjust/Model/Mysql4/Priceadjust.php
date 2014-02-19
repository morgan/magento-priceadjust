<?php
 /**
 * Ability to mass price adjust using rules.
 * 
 * @package		PriceAdjust
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2014 Micheal Morgan
 * @license		MIT
 */
class MagnifyStudio_PriceAdjust_Model_Mysql4_Priceadjust extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{   
		$this->_init('priceadjust/priceadjust', 'priceadjust_id');
	}
}

<?php
/**
 * Ability to mass price adjust using rules.
 * 
 * @package		PriceAdjust
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2014 Micheal Morgan
 * @license		MIT
 */
class MagnifyStudio_PriceAdjust_Model_Priceadjust extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		parent::_construct();
		
		$this->_init('priceadjust/priceadjust');
	}
}

<?php
/**
 * Ability to mass price adjust using rules.
 * 
 * @package		PriceAdjust
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2014 Micheal Morgan
 * @license		MIT
 */
class MagnifyStudio_PriceAdjust_AdjustController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * priceadjust/adjust/price
	 * 
	 * @access	public
	 * @return	void
	 */
	public function priceAction() 
	{
		$this->loadLayout();
		$this->renderLayout();
	}
}

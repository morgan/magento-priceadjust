<?php
/**
 * Ability to mass price adjust using rules.
 * 
 * @package		PriceAdjust
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2014 Micheal Morgan
 * @license		MIT
 */
class MagnifyStudio_PriceAdjust_Model_Observer
{
	public function addMassAction($observer)
	{
		$block = $observer->getEvent()->getBlock();

		if (get_class($block) == 'Mage_Adminhtml_Block_Widget_Grid_Massaction'
			&& $block->getRequest()->getControllerName() == 'catalog_product')
		{
			$block->addItem('priceadjust', array(
				'label'	=> 'Price Adjust',
				'url'	=> Mage::app()->getStore()->getUrl('priceadjust/adjust/price'),
			));
		}
	}
}

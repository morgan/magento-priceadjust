<?php
/**
 * Ability to mass price adjust using rules.
 * 
 * @package		PriceAdjust
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2014 Micheal Morgan
 * @license		MIT
 */
class MagnifyStudio_PriceAdjust_Helper_Data extends Mage_Core_Helper_Abstract
{
	/**
	 * Configuration
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_config = array();

	/**
	 * Rules
	 * 
	 * @access	protected
	 * @var		array
	 */
	protected $_rules = array();

	/**
	 * Setup
	 * 
	 * @access	public
	 * @return	void
	 */
	public function setup()
	{
		static $_init = FALSE;
		
		if ( ! $_init)
		{
			$_init = TRUE;
		
			$post = $this->_getRequest()->getPost();
			
			$stores = $this->get_stores();
		
			current($stores);
			
			$this->_config['store']['source'] = (isset($post['website']['source'])) ? $post['website']['source'] : key($stores);
			
			$this->_config['store']['target'] = (isset($post['website']['target'])) ? $post['website']['target'] : key($stores);

            $this->_config['apply_rule_to_special_prices'] = (isset($post['apply_rule_to_special_prices']) ? true : false );
			
			if ( ! empty($post) && isset($post['rules']))
			{
				$this->set_rules($post['rules']);
			}
			else
			{
				$this->set_rules($this->pull_rules());
			}
		}
		
		return $this;
	}
	
	/**
	 * Set rules
	 * 
	 * @access	public
	 * @param	array
	 * @return	self
	 */
	public function set_rules(array $rules)
	{
		$this->_rules = $rules;
		
		return $this;
	}
	
	/**
	 * Get Rules
	 * 
	 * @access	public
	 * @return	array
	 */
	public function get_rules()
	{
		return $this->_rules;
	}
	
	/**
	 * Get Configuration
	 * 
	 * @access	public
	 * @return	array
	 */
	public function get_config()
	{
		return $this->_config;
	}
	
	/**
	 * Pull rules from database
	 * 
	 * @access	public
	 * @return	array
	 */
	public function pull_rules()
	{
		$rules = array();
	
		$collection = Mage::getModel('priceadjust/priceadjust')->getCollection();
		
		foreach ($collection as $item)
		{
			$rule = array();
			
			$rule['weight_begin'] 	= $item->getWeight_begin();
			$rule['weight_end']		= $item->getWeight_end();
			$rule['value']			= $item->getValue();
			$rule['type']			= $item->getType();
			
			$rules[] = $rule;
		}
		
		return $rules;
	}
	
	/**
	 * Save Rules
	 * 
	 * @access	public
	 * @return	self
	 */
	public function save_rules()
	{
		$priceadjust_model = Mage::getModel('priceadjust/priceadjust')->getCollection();
		$priceadjust_model->walk('delete');
		
		if ( ! empty($this->_rules))
		{
			foreach ($this->_rules as $rule)
			{
				$priceadjust_model = Mage::getModel('priceadjust/priceadjust');
		
				$priceadjust_model->setWeight_begin($rule['weight_begin']);
				$priceadjust_model->setWeight_end($rule['weight_end']);
				$priceadjust_model->setValue($rule['value']);
				$priceadjust_model->setType($rule['type']);
	
				$priceadjust_model->save();
			}
		}
		
		return $this;
	}
	
	/**
	 * Get stores
	 * 
	 * @access	public
	 * @return	array
	 */
	public function get_stores()
	{
		static $stores;
		
		if ($stores === NULL)
		{	
			$stores = array();
		
			foreach (Mage::app()->getWebsites() as $index => $website)
			{
				foreach ($website->getStores() as $store)
				{
					$stores[$store->getId()] = $website->getName()." - ".$store->getName();
				}
			}
		}
		
		return $stores;
	}
	
	/**
	 * Retrive selected products ids from post or session
	 *
	 * @access	public
	 * @return	array|null
	 */
	public function get_products()
	{
		$session = Mage::getSingleton('adminhtml/session');
		
		$post = $this->_getRequest()->getPost();
		
		if ( ! empty($post) && isset($post['product']))
		{
			$session->setProductIds($this->_getRequest()->getParam('product', NULL));
		}
		
		return $session->getProductIds();
	}
	
	/**
	 * Process Rules
	 * 
	 * @access	public
	 * @param	float	Product weight
	 * @param	float	Product price
     * @param   float   Product special_price
	 * @return	float|bool
	 */
	public function rules($weight, $price, $special_price=false)
	{
		$change = array
		(
			'difference' 	=> 0,
			'percent'		=> 0,
			'price'			=> 0,
            'special_price' => 0
		);
		
		foreach ($this->_rules as $rule)
		{
			if ($weight >= $rule['weight_begin'] && $weight <= $rule['weight_end'])
			{
				$_price = FALSE;
                $_special_price = FALSE;

				switch ($rule['type'])
				{
					// Multiplication
					case 0:
						$_price = $price * $rule['value'];
                        $_special_price = $special_price * $rule['value'];
						break;
						
					// Addition
					case 1:
						$_price = $price + $rule['value'];
                        $_special_price = $special_price + $rule['value'];
						break;
						
					// Subtraction	
					case 2:
						$_price = $price - $rule['value'];
                        $_special_price = $special_price - $rule['value'];
						break;
						
					// Division
					case 3:
						$_price = $price / $rule['value'];
                        $_special_price = $special_price / $rule['value'];
						break;
				}
			
				if ($_price != FALSE)
				{
					$change['difference'] 	= $_price - $price;
					$change['percent']		= ($price != 0) ? ($_price - $price) / $price * 100 : 0;
					$change['price']		= $_price;
                    if ($special_price)
                        $change['special_price']	= $_special_price;
				}
				
				break;
			}
		}
		
		return $change;
	}
	
	/**
	 * Process
	 * 
	 * @access	public
	 * @param	int		Store ID
	 * @param	bool	Whether or not to make the changes
	 * @return	array
	 */
	public function process($commit = FALSE)
	{
		$catalog = array();
		
		$collection = Mage::getModel('catalog/product')
			->getCollection()
			->addStoreFilter($this->_config['store']['source'])
			->addAttributeToSelect('*');
			
		$products = $this->get_products();
			
		if ($products != NULL)
		{	
			$collection->addIdFilter($products);
		}	
			
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
			
		$collection->load();
		
		$count = 0;
		
		foreach ($collection as $product)
		{

            $change = $this->rules($product->getWeight(), $product->getPrice(), $product->getSpecialPrice());
		
			$_product = array
			(
				'id'				=> $product->getId(),
				'sku'				=> $product->getSku(),
				'name'				=> $product->getName(),
				'weight'			=> $product->getWeight(),
				'price'				=> $product->getPrice(),
                'special_price'		=> $product->getSpecialPrice(),
				'change_difference'	=> $change['difference'],
				'change_percent'	=> $change['percent'],
				'change_price'		=> $change['price']
			);
            if ($this->_config['apply_rule_to_special_prices']) {
                $_product['change_special_price'] = $change['special_price'];
            }
			
			$catalog[] = $_product;
			
			if ($commit)
			{
				$count++;

                if ($this->_config['apply_rule_to_special_prices'])
				    $this->_update_product($product->getId(), $change['price'], $change['special_price']);
                else
                    $this->_update_product($product->getId(), $change['price']);
			}
		}
		
		/*
		if ($commit)
		{
			Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Total of %d record(s) were successfully updated', $count));
		}
		*/
		
		return $catalog;
	}
	
	/**
	 * Update product
	 * 
	 * @access	protected
	 * @param	int			Product ID
	 * @param	float		Product Price
     * @param   float       Product Special Price
	 * @return	self
	 */
	protected function _update_product($product_id, $price, $special_price=false)
	{
		static $product_model;
	
		if ($product_model === NULL)
		{
			$product_model = Mage::getModel('catalog/product');
		}
	
		$product_model->setData(array());
		
		$product_model->setStoreId($this->_config['store']['target'])
			->load($product_id)
			->setIsMassupdate(TRUE)
			->setExcludeUrlRewrite(TRUE);
			
		$product_model->addData(array
		(
			'price'	=> $price
		));
        if ($special_price) {
            $product_model->addData(array
            (
                'special_price'	=> $special_price
            ));
        }
		
		$product_model->save();
		
		return $this;
	}
	
	/**
	 * Format Price
	 * 
	 * @access	public
	 * @param	float
	 * @return	string
	 */
	public function format_price($price)
	{
		return '$' . number_format($price, 2, '.', '');
	}
}

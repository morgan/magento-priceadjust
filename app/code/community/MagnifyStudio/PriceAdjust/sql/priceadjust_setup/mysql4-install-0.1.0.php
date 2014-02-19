<?php
/**
 * Ability to mass price adjust using rules.
 * 
 * @package		PriceAdjust
 * @author		Micheal Morgan <micheal@morgan.ly>
 * @copyright	(c) 2014 Micheal Morgan
 * @license		MIT
 */
$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('priceadjust')};
	CREATE TABLE {$this->getTable('priceadjust')} (
		`priceadjust_id` int(11) unsigned NOT NULL auto_increment,
		`weight_begin` float NOT NULL default '0',    
		`weight_end` float NOT NULL default '0',              
		`type` smallint(2) NOT NULL default '0',
		`value` float NOT NULL default '0',
	PRIMARY KEY (`priceadjust_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

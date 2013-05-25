<?php

$installer = $this;
$installer->startSetup(); 

$installer->run("
CREATE TABLE IF NOT EXISTS `{$installer->getTable('Bitcoins/ipn')}` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `quote_id` int(10) unsigned default NULL,
  `order_id` int(10) unsigned default NULL,
  `invoice_id` varchar(200) NOT NULL,
  `url` varchar(400) NOT NULL,
  `status` varchar(20) NOT NULL,
  `btc_price` decimal(16,8) NOT NULL,
  `price` decimal(16,8) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `invoice_time` int(11) unsigned NOT NULL,
  `expiration_time` int(11) unsigned NOT NULL,
  `current_time` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `quote_id` (`quote_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 ;
 
");
 
$installer->endSetup();
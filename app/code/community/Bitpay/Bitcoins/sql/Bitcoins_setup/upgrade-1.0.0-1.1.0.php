<?php

$installer = $this;
$installer->startSetup(); 

$installer->run("ALTER TABLE `{$installer->getTable('Bitcoins/ipn')}` ADD `pos_data` VARCHAR( 256 ) NOT NULL AFTER `url`;");
 
$installer->endSetup();
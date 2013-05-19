<?php

$installer = $this;
$installer->startSetup(); 

$installer->run("ALTER TABLE `bitpay_ipns` ADD `pos_data` VARCHAR( 256 ) NOT NULL AFTER `url`;");
 
$installer->endSetup();
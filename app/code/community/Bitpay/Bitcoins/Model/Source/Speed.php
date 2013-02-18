<?php

class Bitpay_Bitcoins_Model_Source_Speed
{
    public function toOptionArray()
    {
	
		return array(
			array(
				'value' => 'low',
				'label' => 'Low',
				),
			array(
				'value' => 'medium',
				'label' => 'Medium',
				),
			array(
				'value' => 'high',
				'label' => 'High',
			));	
    }
}

?>
<?php

class Bitpay_Bitcoins_Model_Ipn extends Mage_Core_Model_Abstract
{
	function _construct()
	{
		$this->_init('Bitcoins/ipn');
		return parent::_construct();
	}
	
	function Record($invoice)
	{
		return $this
			->setQuoteId(isset($invoice['posData']['quoteId']) ? $invoice['posData']['quoteId'] : NULL)
			->setOrderId(isset($invoice['posData']['orderId']) ? $invoice['posData']['orderId'] : NULL)
			->setInvoiceId($invoice['id'])
			->setUrl($invoice['url'])
			->setStatus($invoice['status'])
			->setBtcPrice($invoice['btcPrice'])
			->setPrice($invoice['price'])
			->setCurrency($invoice['currency'])
			->setInvoiceTime(intval($invoice['invoiceTime']/1000.0))
			->setExpirationTime(intval($invoice['expirationTime']/1000.0))
			->setCurrentTime(intval($invoice['currentTime']/1000.0))
			->save();
	}
	
	function GetStatusReceived($quoteId, $statuses)
	{
		$collection = $this->getCollection()->AddFilter('quote_id', $quoteId);
		foreach($collection as $i)
			if (in_array($i->getStatus(), $statuses))
				return true;
				
		return false;		
	}
	
	function GetQuotePaid($quoteId)
	{
		return $this->GetStatusReceived($quoteId, array('paid', 'confirmed', 'complete'));
	}
	
	function GetQuoteComplete($quoteId)
	{
		return $this->GetStatusReceived($quoteId, array('confirmed', 'complete'));
	}
	
	

}

?>
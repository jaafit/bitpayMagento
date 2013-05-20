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
			->setPosData(json_encode($invoice['posData']))
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
		if (!$quoteId)
			return false;
					
		$quote = Mage::getModel('sales/quote')->load($quoteId, 'entity_id');
		if (!$quote)
		{
			Mage::log('quote not found', NULL, 'bitpay.log');
			return false;
		}
		
		$quoteHash = Mage::getModel('Bitcoins/paymentMethod')->getQuoteHash($quoteId);
		if (!$quoteHash)
		{
			Mage::log('Could not find quote hash for quote '.$quoteId, NULL, 'bitpay.log');
			return false;		
		}
			
		$collection = $this->getCollection()->AddFilter('quote_id', $quoteId);
		foreach($collection as $i)
		{
			if (in_array($i->getStatus(), $statuses))
			{
				// check that quote data was not updated after IPN sent
				$posData = json_decode($i->getPosData());
				if (!$posData)
					continue;
					
				if ($quoteHash == $posData->quoteHash)
					return true;
			}
		}
				
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
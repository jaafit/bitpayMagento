<?php

class Bitpay_Bitcoins_Block_Iframe extends Mage_Checkout_Block_Onepage_Payment
{
	protected function _construct()
    {      
        $this->setTemplate('bitcoins/iframe.phtml');
		parent::_construct();
    }
	
	// create an invoice and return the url so that iframe.phtml can display it
	public function GetIframeUrl()
	{			
		if (Mage::getStoreConfig('payment/Bitcoins/fullscreen'))
			return 'disabled';
		
		include 'lib/bitpay/bp_lib.php';		

		$apiKey = Mage::getStoreConfig('payment/Bitcoins/api_key');
		$speed = Mage::getStoreConfig('payment/Bitcoins/speed');
		
		$quote = $this->getQuote();
		$quoteId = $quote->getId();
		if (Mage::getModel('Bitcoins/ipn')->GetQuotePaid($quote->getId()))
			return 'paid'; // quote's already paid, so don't show the iframe
			
		$options = array(
			'currency' => $quote->getQuoteCurrencyCode(),
			#'buyerName' => $order->getCustomerFirstname().' '.$order->getCustomerLastname(),			
			'fullNotifications' => 'true',
			'notificationURL' => Mage::getUrl('bitpay_callback'),
			'redirectURL' => Mage::getUrl('customer/account'),
			'transactionSpeed' => $speed,
			'apiKey' => $apiKey,
			);
		$invoice = bpCreateInvoice($quoteId, $quote->getGrandTotal(), array('quoteId' => $quoteId), $options);
					
		if (array_key_exists('error', $invoice)) 
		{
			Mage::log('Error creating bitpay invoice');
			Mage::log($invoice['error']);
			Mage::throwException("Error creating bit-pay invoice.  Please try again or use another payment option.");
			return false; 
		}
		
		return $invoice['url'].'&view=iframe';
	}
}

?>	
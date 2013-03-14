<?php

class Bitpay_Bitcoins_Block_Iframe extends Mage_Checkout_Block_Onepage_Payment
{
	protected function _construct()
    {      
        $this->setTemplate('bitcoins/iframe.phtml');
		parent::_construct();
    }
	
	public function GetQuoteId()
	{
		$quote = $this->getQuote();
		$quoteId = $quote->getId();
		return $quoteId;
	}
		
	
	// create an invoice and return the url so that iframe.phtml can display it
	public function GetIframeUrl()
	{	
		// are they using bitpay?
		if (!($quote = Mage::getSingleton('checkout/session')->getQuote()) 
			or !($payment = $quote->getPayment())
			or !($instance = $payment->getMethodInstance())
			or ($instance->getCode() != 'Bitcoins'))
			return 'notbitpay';
		
		// fullscreen disabled?
		if (Mage::getStoreConfig('payment/Bitcoins/fullscreen'))
			return 'disabled';
		
		include 'lib/bitpay/bp_lib.php';		

		$apiKey = Mage::getStoreConfig('payment/Bitcoins/api_key');
		$speed = Mage::getStoreConfig('payment/Bitcoins/speed');
		
		$quote = $this->getQuote();
		$quoteId = $quote->getId();
		if (Mage::getModel('Bitcoins/ipn')->GetQuotePaid($quoteId))
			return 'paid'; // quote's already paid, so don't show the iframe
			
		$options = array(
			'currency' => $quote->getQuoteCurrencyCode(),
			'fullNotifications' => 'true',
			'notificationURL' => Mage::getUrl('bitpay_callback'),
			'redirectURL' => Mage::getUrl('customer/account'),
			'transactionSpeed' => $speed,
			'apiKey' => $apiKey,
			);
			
		// customer data
		$method = Mage::getModel('Bitcoins/paymentMethod');
		$options += $method->ExtractAddress($quote->getShippingAddress());

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
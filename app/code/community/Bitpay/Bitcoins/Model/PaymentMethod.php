<?php
 
/**
* Our test CC module adapter
*/
class Bitpay_Bitcoins_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
	/**
	* unique internal payment method identifier
	*
	* @var string [a-z0-9_]
	*/
	protected $_code = 'Bitcoins';
 
	/**
	 * Here are examples of flags that will determine functionality availability
	 * of this module to be used by frontend and backend.
	 *
	 * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
	 *
	 * It is possible to have a custom dynamic logic by overloading
	 * public function can* for each flag respectively
	 */
	 
	/**
	 * Is this payment method a gateway (online auth/charge) ?
	 */
	protected $_isGateway               = true;
 
	/**
	 * Can authorize online?
	 */
	protected $_canAuthorize            = true;
 
	/**
	 * Can capture funds online?
	 */
	protected $_canCapture              = false;
 
	/**
	 * Can capture partial amounts online?
	 */
	protected $_canCapturePartial       = false;
 
	/**
	 * Can refund online?
	 */
	protected $_canRefund               = false;
 
	/**
	 * Can void transactions online?
	 */
	protected $_canVoid                 = false;
 
	/**
	 * Can use this payment method in administration panel?
	 */
	protected $_canUseInternal          = false;
 
	/**
	 * Can show this payment method as an option on checkout payment page?
	 */
	protected $_canUseCheckout          = true;
 
	/**
	 * Is this payment method suitable for multi-shipping checkout?
	 */
	protected $_canUseForMultishipping  = true;
 
	/**
	 * Can save credit card information for future processing?
	 */
	protected $_canSaveCc = false;
	
	//protected $_formBlockType = 'bitcoins/form';
	//protected $_infoBlockType = 'bitcoins/info';
	
	function canUseForCurrency($currencyCode)
	{		
		$currencies = Mage::getStoreConfig('payment/Bitcoins/currencies');
		$currencies = array_map('trim', explode(',', $currencies));
		return array_search($currencyCode, $currencies) !== false;
	}
 	
	public function canUseCheckout()
    {
		
		$secret = Mage::getStoreConfig('payment/Bitcoins/api_key');
		if (!$secret or !strlen($secret))
		{
			Mage::log('Bitpay/Bitcoins: API key not entered');
			return false;
		}
		
		$speed = Mage::getStoreConfig('payment/Bitcoins/speed');
		if (!$speed or !strlen($speed))
		{
			Mage::log('Bitpay/Bitcoins: Transaction Speed invalid');
			return false;
		}
		
		return $this->_canUseCheckout;
    }

	public function authorize(Varien_Object $payment, $amount) 
	{
		$quoteId = $payment->getOrder()->getQuoteId();
		$ipn = Mage::getModel('Bitcoins/ipn');
		if (!$ipn->GetQuotePaid($quoteId))
		{
			Mage::throwException("Order not paid for.  Please pay first and then Place your Order.");
		}
		else if (!$ipn->GetQuoteComplete($quoteId))
		{			
			// order status will be PAYMENT_REVIEW instead of PROCESSING
			$payment->setIsTransactionPending(true); 
		}
		
		return $this;
	}
}
?>
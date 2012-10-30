<?

// callback controller
class Bitpay_Bitcoins_IndexController extends Mage_Core_Controller_Front_Action {
	
	// bitpay's IPN lands here
	public function indexAction() {		
		require 'lib/bitpay/bp_lib.php';
		
		$apiKey = Mage::getStoreConfig('payment/Bitcoins/api_key');
		$invoice = bpVerifyNotification($apiKey);
		
		if (is_string($invoice))
			Mage::log("bitpay callback error: $invoice");
		else {
			$quoteId = $invoice['posData'];
			$bitpayIpn = Mage::getModel('Bitcoins/ipn');
			
			// save the ipn so that we can find it when the user clicks "Place Order"
			$bitpayIpn->Record($invoice); 		
			
			// update the order if it exists already
			$order = Mage::getModel('sales/order')->load($quoteId, 'quote_id');
			if ($order->getId())
				switch($invoice['status'])
				{					
				case 'confirmed':							
				case 'complete':
					$invoices = $order->getInvoiceCollection();
					foreach($invoices as $i)
						$i->pay()
							->save();
					$order->setState($order::STATE_PROCESSING, true)
						->save();
					break;
				}				
		}
	}

}


?>
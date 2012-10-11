<?

class Bitpay_Bitcoins_IndexController extends Mage_Core_Controller_Front_Action {

	function debuglog($contents)
	{
		file_put_contents('lib/bitpay/log.txt', "\n".date('m-d H:i:s').": ", FILE_APPEND);
		file_put_contents('lib/bitpay/log.txt', $contents, FILE_APPEND);
	}

    public function indexAction() {
		require 'lib/bitpay/bp_lib.php';
		
		$apiKey = Mage::getStoreConfig('payment/Bitcoins/api_key');
		$response = bpVerifyNotification($apiKey);
		if (is_string($response))
			$this->debuglog("bitpay callback error: $response");
		else {
			$orderId = $response['posData'];
			$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
			$this->debuglog($response['status']);

			switch($response['status'])
			{
				case 'paid':
					$order->setState($order::STATE_PROCESSING, true)->save();
					break;
					
				case 'confirmed':							
				case 'complete':
					$invoices = $order->getInvoiceCollection();
					foreach($invoices as $i)
						$i->pay()
							->save();
					$order->setState($order::STATE_PROCESSING, true)
						->save();
					break;
				
				// (bit-pay.com does not send expired notifications as of this release)
				case 'expired':			
					// could set invoices to canceled
					break;

			}
		}
		
    }
}


?>
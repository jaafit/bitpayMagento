<?

class Bitpay_Bitcoins_IndexController extends Mage_Core_Controller_Front_Action {

	function debuglog($contents)
	{
		$file = 'lib/bitpay/log.txt';
		file_put_contents($file, date('m-d H:i').": \n", FILE_APPEND);
		if (is_array($contents))
			foreach($contents as $k => $v)
				file_put_contents($file, $k.': '.$v."\n", FILE_APPEND);
		else
			file_put_contents($file, $contents."\n", FILE_APPEND);
	}

    public function indexAction() {
		require 'lib/bitpay/bp_lib.php';
		
		$apiKey = Mage::getStoreConfig('payment/Bitcoins/api_key');
		$response = bpVerifyNotification($apiKey);
		if (is_string($response))
			$this->debuglog("bitpay callback error: $response");
		else {
			$orderId = $response['posData'];
			switch($response['status'])
			{
				case 'confirmed':							
					$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
					$invoices = $order->getInvoiceCollection();
					foreach($invoices as $i)
						$i->pay()
							->save();
					$order->setState($order::STATE_PROCESSING)
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
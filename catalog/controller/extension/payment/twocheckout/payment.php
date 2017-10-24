<?php
require_once("lib/Twocheckout.php");
class ControllerExtensionPaymentTwoCheckoutPayment extends Controller {
	
	public function index(){
		
	}
	
	public function pay($ccNo,$cvv,$expMonth,$expYear){
		$sellerId = '901356105';
		$publishableKey = "EF01FFB8-BCBE-44BA-9A25-434EB3F2C8F8";
		$data['sellerId'] = $sellerId;
		$data['publishableKey'] = $publishableKey;
		$data['ccNo'] = $ccNo;
		$data['cvv'] = $cvv;
		$data['expMonth'] = $expMonth;
		$data['expYear'] = $expYear;

		$this->response->setOutput($this->load->view('extension/payment/twocheckout/checkout', $data));
	}
	
	public function auth(){
		$sellerId = '901356105';
		$privateKey = '84A6006D-0799-41D3-AA43-2AE3C9630A45';
		Twocheckout::privateKey($privateKey); //Private Key
		Twocheckout::sellerId($sellerId); // 2Checkout Account Number
		Twocheckout::sandbox(true); // Set to false for production accounts.
		//print_r($this->request->post['token']);
		try {
			$charge = Twocheckout_Charge::auth(array(
				"merchantOrderId" => "123",
				"token"      => $this->request->post['token'],
				"currency"   => 'USD',
				"total"      => '10.00',
				"billingAddr" => array(
					"name" => 'Testing Tester',
					"addrLine1" => '123 Test St',
					"city" => 'Columbus',
					"state" => 'OH',
					"zipCode" => '43123',
					"country" => 'USA',
					"email" => 'example@2co.com',
					"phoneNumber" => '555-555-5555'
				)
			));
		
			if ($charge['response']['responseCode'] == 'APPROVED') {
				echo "Thanks for your Order!";
				echo "<h3>Return Parameters:</h3>";
				echo "<pre>";
				print_r($charge);
				echo "</pre>";
		
			}
		} catch (Twocheckout_Error $e) {
			print_r($e->getMessage());
		}
	}

}
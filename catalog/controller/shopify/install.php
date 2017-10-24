<?php

	# install.php?shop=example-shop.myshopify.com

	require __DIR__.'/vendor/autoload.php';
	use phpish\shopify;
	
	class ControllerShopifyInstall extends Controller {

		public function index(){
			
			
			
			# Guard
	isset($_GET['shop']) or die ('Query parameter "shop" missing.');
	preg_match('/^[a-zA-Z0-9\-]+.myshopify.com$/', $_GET['shop']) or die('Invalid myshopify.com store URL.');
$this->session->data['install'] = 'true';
	$install_url = shopify\install_url($_GET['shop'], SHOPIFY_APP_API_KEY);
	
	echo "<script> top.location.href='$install_url'</script>";
		}
	
	}

?>
<?php




class ControllerCommoniplPublishlist extends Controller {
	
	public function index() {

		$this->load->language('commonipl/product');
		$this->load->model('commonipl/product');
		
		$products = $this->model_commonipl_product->getPublishProduct();
		$productList = array();
		foreach($products as $product){
			$p = json_decode($product['shopify_product_json'],true);
			
			
			if(isset($p['name'])){
				
				if ($p['images']) {
				$image =$p['images'][0]['src'] ;
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
			}
				$productList[] = array(
					'name'=>$p['name'],
					'image'=>$image,
					'status'=>$p['status'],
					'published_at'=>date($this->language->get('date_format_short'), strtotime($product['date_added'])),
					'sales'=>0,
					'href'        => ''
				);
			}
		}
		//print_r($productList);
		$data['products'] = $productList;
		$data['footer'] = $this->load->controller($this->session->data['store'].'/footer');
		$data['header'] = $this->load->controller($this->session->data['store'].'/header');
		$this->response->setOutput($this->load->view('commonipl/publish_list', $data));
	}
	

}

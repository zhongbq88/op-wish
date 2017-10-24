<?php




class ControllerCommoniplProductlist extends Controller {
	
	public function index() {

		$this->load->language('commonipl/product');
		$data['product_views'] = array();
		$product_view = $this->load->controller('commonipl/products');
		if (isset($this->request->get['variant_index'])) {
			$json['success'] = $product_view;
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
		for($i=0;$i<4;$i++){
			$data['product_views'][] =  $product_view;
		}
		$data['footer'] = $this->load->controller($this->session->data['store'].'/footer');
		$data['header'] = $this->load->controller($this->session->data['store'].'/header');
		$this->response->setOutput($this->load->view('commonipl/product_list', $data));
	}
	

}

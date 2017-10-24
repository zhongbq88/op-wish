<?php
class ControllerStoreCoupon extends Controller {

	public function index() {
		if(isset($this->request->get['tracking'])){
			
		}
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('store/coupon', $data));
	}


}

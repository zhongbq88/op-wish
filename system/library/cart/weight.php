<?php
namespace Cart;
class Weight {
	private $weights = array();

	public function __construct($registry) {
		$this->db = $registry->get('db');
		$this->config = $registry->get('config');

		$weight_class_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class wc LEFT JOIN " . DB_PREFIX . "weight_class_description wcd ON (wc.weight_class_id = wcd.weight_class_id) WHERE wcd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($weight_class_query->rows as $result) {
			$this->weights[$result['weight_class_id']] = array(
				'weight_class_id' => $result['weight_class_id'],
				'title'           => $result['title'],
				'unit'            => $result['unit'],
				'value'           => $result['value']
			);
		}
	}

	public function convert($value, $from, $to) {
		if ($from == $to) {
			return $value;
		}

		if (isset($this->weights[$from])) {
			$from = $this->weights[$from]['value'];
		} else {
			$from = 1;
		}

		if (isset($this->weights[$to])) {
			$to = $this->weights[$to]['value'];
		} else {
			$to = 1;
		}

		return $value * ($to / $from);
	}

	public function format($value, $weight_class_id, $decimal_point = '.', $thousand_point = ',') {
		if (isset($this->weights[$weight_class_id])) {
			return number_format($value, 2, $decimal_point, $thousand_point) . $this->weights[$weight_class_id]['unit'];
		} else {
			return number_format($value, 2, $decimal_point, $thousand_point);
		}
	}

	public function getUnit($weight_class_id) {
		if (isset($this->weights[$weight_class_id])) {
			return $this->weights[$weight_class_id]['unit'];
		} else {
			return '';
		}
	}
	
	public function formatCost($shipping, $weight) {
		if(!isset($shipping)){
			return 0;
		}
		//print_r($shipping['maxWeight']);
		foreach($shipping['maxWeight'] as $key=> $maxWeight){
			 if($shipping['minWeight']>=$weight && $maxWeight>=$weight){
				$cost = $shipping['firstWeightPrice'][$key];
				if($shipping['firstWeight'][$key]<$weight){
					$cost += ($weight-$shipping['firstWeight'][$key])/$shipping['perWeight'][$key]*$shipping['perWeightPrice'][$key];
				}
				if(isset($shipping['registeredFee'][$key]))
				$cost +=$shipping['registeredFee'][$key];
				if(isset($shipping['additionalFee'][$key]))
				$cost +=$shipping['additionalFee'][$key];
				if(isset($shipping['unitWeight'][$key]))
				$cost += ($weight)/$shipping['unitWeight'][$key]*$shipping['unitWeightPrice'][$key];
					if(isset($shipping['fuelOilFeePercent'][$key]))
				$cost += $cost*$shipping['fuelOilFeePercent'][$key]/100;
				return $cost;
			}
		}
		return 0;
	}
}
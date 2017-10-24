<?php 
class ModelShippingByTotalPercent extends Model {
	public function getQuote($address) {
		$this->load->language('shipping/by_total_percent');

		$quote_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name");

		foreach ($query->rows as $result) {
			if ($this->config->get('by_total_percent_' . $result['geo_zone_id'] . '_status')) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$result['geo_zone_id'] . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

				if ($query->num_rows) {
					$status = true;
				} else {
					$status = false;
				}
        
			} else {
				$status = false;
			}

      $method_data = array();
      
			if ($status) {
				$cost_total = 0;
				
				$weight = $this->cart->getWeight();
				if ($this->config->get('by_total_percent_' . $result['geo_zone_id'] . '_rate_weight') != '') {
					$cost_weight = 0;
					$rates_weight = explode(',', $this->config->get('by_total_percent_' . $result['geo_zone_id'] . '_rate_weight'));
					foreach ($rates_weight as $rate) {
						$data = explode(':', $rate);

						if ($data[0] >= $weight) {
							if (isset($data[1])) {
								$cost_weight = $data[1];
							}
							break;
						}
					}
					$cost_total += $cost_weight;
				}
				
				$cart_total = $this->cart->getTotal();
				if ($this->config->get('by_total_percent_' . $result['geo_zone_id'] . '_rate_total_summa') != '') {
					$cost_total_summa = 0;
					$rates_total_summa = explode(',', $this->config->get('by_total_percent_' . $result['geo_zone_id'] . '_rate_total_summa'));
					foreach ($rates_total_summa as $rate) {
						$data = explode(':', $rate);

						if ($data[0] >= $cart_total) {
							if (isset($data[1])) {
								$cost_total_summa = $data[1];
							}
							break;
						}
					}
					$cost_total += $cost_total_summa;
				}
				
				$cart_total = $this->cart->getTotal();
				if ($this->config->get('by_total_percent_' . $result['geo_zone_id'] . '_rate_percent') != '') {
					$cost_percent = 0;
					$rates_percent = explode(',', $this->config->get('by_total_percent_' . $result['geo_zone_id'] . '_rate_percent'));
					foreach ($rates_percent as $rate) {
						$data = explode(':', $rate);

						if ($data[0] >= $cart_total) {
							if (isset($data[1])) {
								$cost_percent = $cart_total / 100 * $data[1];
							}

							break;
						}
					}
					$cost_total += $cost_percent;
				}	

				if ($cost_total > 0) {
					$quote_data['by_total_percent_' . $result['geo_zone_id']] = array(
						'code'         => 'by_total_percent.by_total_percent_' . $result['geo_zone_id'],
						'title'        => $result['name'] . '  (' . $this->language->get('text_total') . ' ' . $this->currency->format($this->tax->calculate($cart_total, $this->config->get('by_total_percent_tax_class_id'), $this->config->get('config_tax'))) . ')',
						'cost'         => $cost_total,
						'tax_class_id' => $this->config->get('by_total_percent_tax_class_id'),
						'text'         => $this->currency->format($this->tax->calculate($cost_total, $this->config->get('by_total_percent_tax_class_id'), $this->config->get('config_tax')))
					);
				} elseif ($cost_total == 0) {
					$quote_data['by_total_percent_' . $result['geo_zone_id']] = array(
						'code'         => 'by_total_percent.by_total_percent_' . $result['geo_zone_id'],
						'title'        => $result['name'] . '  (' . $this->language->get('text_total') . ' ' . $this->currency->format($this->tax->calculate($cart_total, $this->config->get('by_total_percent_tax_class_id'), $this->config->get('config_tax'))) . ')',
						'cost'         => 0.00,
						'tax_class_id' => $this->config->get('by_total_percent_tax_class_id'),
						'text'         => $this->currency->format(0.00)
					);
				}
			}
		}

		if ($quote_data) {
      $method_data = array(
        'code'       => 'by_total_percent',
        'title'      => $this->language->get('text_title'),
        'quote'      => $quote_data,
        'sort_order' => $this->config->get('by_total_percent_sort_order'),
        'error'      => false
      );
    }

		return $method_data;
	}
}
?>
<?php
class ModelExtensionPaymentFintreen extends Model {
    public function getMethod($address, $total) {
        $this->load->language('extension/payment/fintreen');

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('payment_fintreen_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

        if ($this->config->get('payment_fintreen_total') > 0 && $this->config->get('payment_fintreen_total') > $total) {
            $status = false;
        } elseif (!$this->config->get('payment_fintreen_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        $method_data = array();

        if ($status) {
            $method_data = array(
                'code'       => 'fintreen',
                'title'      => $this->language->get('text_title'),
                'terms'      => '',
                'sort_order' => $this->config->get('payment_fintreen_sort_order')
            );
        }

        return $method_data;
    }

    public function createTransaction($amount, $currency, $order_id) {
        $this->load->library('fintreen');

        $fintreen = new Fintreen($this->config->get('payment_fintreen_token'), $this->config->get('payment_fintreen_email'), $this->config->get('payment_fintreen_test'));

        $result = $fintreen->createTransaction($amount, $currency);

        if ($result && isset($result['data']['id'])) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "fintreen_transaction SET order_id = '" . (int)$order_id . "', fintreen_id = '" . (int)$result['data']['id'] . "', amount = '" . (float)$amount . "', currency = '" . $this->db->escape($currency) . "', date_added = NOW()");

            return $result['data'];
        }

        return false;
    }

    public function checkTransaction($transaction_id) {
        $this->load->library('fintreen');

        $fintreen = new Fintreen($this->config->get('payment_fintreen_token'), $this->config->get('payment_fintreen_email'), $this->config->get('payment_fintreen_test'));

        return $fintreen->checkTransaction($transaction_id);
    }

    public function getOrderIdByTransactionId($transaction_id) {
        $query = $this->db->query("SELECT order_id FROM " . DB_PREFIX . "fintreen_transaction WHERE fintreen_id = '" . (int)$transaction_id . "'");

        if ($query->num_rows) {
            return $query->row['order_id'];
        }

        return false;
    }

    public function install() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "fintreen_transaction` (
              `fintreen_transaction_id` INT(11) NOT NULL AUTO_INCREMENT,
              `order_id` INT(11) NOT NULL,
              `fintreen_id` INT(11) NOT NULL,
              `amount` DECIMAL(15,4) NOT NULL,
              `currency` VARCHAR(3) NOT NULL,
              `date_added` DATETIME NOT NULL,
              PRIMARY KEY (`fintreen_transaction_id`)
            ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
        ");
    }

    public function uninstall() {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "fintreen_transaction`");
    }
}
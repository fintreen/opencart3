<?php
class ControllerExtensionPaymentFintreen extends Controller {
    public function index() {
        $this->load->language('extension/payment/fintreen');

        $data['button_confirm'] = $this->language->get('button_confirm');

        $data['action'] = $this->url->link('extension/payment/fintreen/confirm', '', true);

        return $this->load->view('extension/payment/fintreen', $data);
    }

    public function confirm() {
        if ($this->session->data['payment_method']['code'] == 'fintreen') {
            $this->load->model('checkout/order');

            $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

            $amount = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);

            $fintreen_transaction = $this->createFintreenTransaction($amount, $order_info['currency_code']);

            if ($fintreen_transaction) {
                $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_fintreen_order_status_id'));

                $this->response->redirect($fintreen_transaction['link']);
            } else {
                $this->response->redirect($this->url->link('checkout/checkout', '', true));
            }
        }
    }

    private function createFintreenTransaction($amount, $currency) {
        $this->load->model('extension/payment/fintreen');

        return $this->model_extension_payment_fintreen->createTransaction($amount, $currency);
    }

    public function callback() {
        $this->load->model('extension/payment/fintreen');

        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['transaction_id'])) {
            $transaction_id = $input['transaction_id'];

            $result = $this->model_extension_payment_fintreen->checkTransaction($transaction_id);

            if ($result && $result['status'] == 'success') {
                $order_id = $this->model_extension_payment_fintreen->getOrderIdByTransactionId($transaction_id);

                if ($order_id) {
                    $this->load->model('checkout/order');
                    $this->model_checkout_order->addOrderHistory($order_id, $this->config->get('payment_fintreen_order_status_id'), 'Payment confirmed by Fintreen', true);
                }

                $this->response->addHeader('HTTP/1.1 200 OK');
                $this->response->setOutput(json_encode(['success' => true]));
            } else {
                $this->response->addHeader('HTTP/1.1 400 Bad Request');
                $this->response->setOutput(json_encode(['success' => false, 'message' => 'Transaction not successful']));
            }
        } else {
            $this->response->addHeader('HTTP/1.1 400 Bad Request');
            $this->response->setOutput(json_encode(['success' => false, 'message' => 'Invalid input']));
        }
    }
}
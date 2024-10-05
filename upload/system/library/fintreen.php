<?php
// system/library/fintreen.php

class Fintreen {
    private $token;
    private $email;
    private $test;
    private $url = 'https://fintreen.com/api/v1/';

    public function __construct($token, $email, $test = false) {
        $this->token = $token;
        $this->email = $email;
        $this->test = $test;
    }

    public function createTransaction($amount, $currency) {
        $endpoint = 'create';
        $data = array(
            'fiatAmount' => $amount,
            'fiatCode' => $currency,
            'cryptoCode' => 'BTC', // You might want to make this configurable
            'isTest' => $this->test ? 1 : 0
        );

        return $this->sendRequest($endpoint, $data, 'POST');
    }

    public function checkTransaction($transactionId) {
        $endpoint = 'check';
        $data = array(
            'orderId' => $transactionId,
            'isTest' => $this->test ? 1 : 0
        );

        return $this->sendRequest($endpoint, $data, 'GET');
    }

    private function sendRequest($endpoint, $data, $method = 'GET') {
        $url = $this->url . $endpoint;

        $headers = array(
            'Content-Type: application/json',
            'Accept: application/json',
            'fintreen_auth: ' . $this->token,
            'fintreen_signature: ' . $this->createSignature()
        );

        $ch = curl_init();

        if ($method === 'GET') {
            $url .= '?' . http_build_query($data);
        } else {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }

        return json_decode($response, true);
    }

    private function createSignature() {
        return sha1($this->token . $this->email);
    }

    public function getCurrenciesList() {
        $endpoint = 'currencies';
        return $this->sendRequest($endpoint, array(), 'GET');
    }

    public function getOrderStatusList() {
        $endpoint = 'order/statuses';
        return $this->sendRequest($endpoint, array(), 'GET');
    }

    public function calculate($amount, $cryptoCodes, $fiatCode = 'EUR') {
        $endpoint = 'calculate';
        $data = array(
            'fiatAmount' => $amount,
            'fiatCode' => $fiatCode,
            'cryptoCodes' => is_array($cryptoCodes) ? implode(',', $cryptoCodes) : $cryptoCodes
        );

        return $this->sendRequest($endpoint, $data, 'GET');
    }

    public function getTransactionsList($filters = array()) {
        $endpoint = 'transactions';
        $filters['isTest'] = $this->test ? 1 : 0;
        return $this->sendRequest($endpoint, $filters, 'GET');
    }
}
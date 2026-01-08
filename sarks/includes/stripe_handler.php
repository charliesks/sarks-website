<?php

class StripeHandler
{
    private $secret_key;
    private $api_base = "https://api.stripe.com/v1";

    public function __construct($secret_key)
    {
        $this->secret_key = $secret_key;
    }

    public function createCheckoutSession($params)
    {
        return $this->request('POST', '/checkout/sessions', $params);
    }

    public function retrieveSession($session_id)
    {
        return $this->request('GET', '/checkout/sessions/' . $session_id);
    }

    private function request($method, $endpoint, $params = [])
    {
        $url = $this->api_base . $endpoint;
        $ch = curl_init();

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } else if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->secret_key . ":");
        curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem'); // In case of SSL issues, but standard certs usually work

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($response, true);

        if ($http_code < 200 || $http_code >= 300) {
            throw new Exception("Stripe API Error ($http_code): " . ($data['error']['message'] ?? $response));
        }

        return $data;
    }
}

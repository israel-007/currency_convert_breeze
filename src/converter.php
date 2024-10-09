<?php

namespace converter;

use Exception;

class converter
{
    private $apiUrl = 'https://api.exchangerate-api.com/v4/latest/';  // Replace with your preferred currency API
    private $baseCurrency;
    private $targetCurrencies = [];
    private $amount = 1;  // Default amount is 1
    private $cookieExpiry = 300; // 5 minutes in seconds

    public function __construct($data = [])
    {
        $this->baseCurrency = strtoupper('usd');  // Ensure baseCurrency is always uppercase
        (isset($data['timeout'])) ? $this->cookieExpiry = $data['timeout'] : '';
    }

    // Chainable method to set the base currency
    public function from($baseCurrency)
    {
        $this->baseCurrency = strtoupper($baseCurrency);
        return $this;
    }

    // Chainable method to set target currencies
    public function to(array $targetCurrencies)
    {
        $this->targetCurrencies = array_map('strtoupper', $targetCurrencies);  // Ensure all target currencies are uppercase
        return $this;
    }

    // Chainable method to set the amount
    public function amount($amount = 1)
    {
        if (!is_numeric($amount) || $amount <= 0) {
            throw new Exception("Amount must be a positive number.");
        }
        $this->amount = $amount;
        return $this;
    }

    // Final method to execute the conversion with optional JSON output
    public function run($outputType = 'array')
    {
        try {
            if (!$this->baseCurrency || empty($this->targetCurrencies)) {
                throw new Exception("Base currency and target currencies must be set before calling run().");
            }

            $exchangeRates = $this->getExchangeRates($this->baseCurrency);
            $convertedValues = [];

            foreach ($this->targetCurrencies as $currency) {
                if (isset($exchangeRates[$currency])) {
                    $convertedValues[$currency] = $this->amount * $exchangeRates[$currency];
                } else {
                    throw new Exception("Currency conversion rate for {$currency} not available.");
                }
            }

            $response = [
                'status' => 'success',
                'values' => $convertedValues
            ];

        } catch (Exception $e) {
            // Handle errors by returning status as "error" with message
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        // Return response in either array or JSON format
        return ($outputType === 'json') ? json_encode($response) : $response;
    }

    // Private method to fetch exchange rates (same as before)
    private function getExchangeRates($currency)
    {
        // Check if exchange rates are already saved in a cookie
        if (isset($_COOKIE["exchange_rates_{$currency}"])) {
            $exchangeRates = json_decode($_COOKIE["exchange_rates_{$currency}"], true);
            if (json_last_error() === JSON_ERROR_NONE && $exchangeRates) {
                return $exchangeRates;
            }
        }

        // Fetch from API if not available in cookie
        $url = $this->apiUrl . $currency;
        $exchangeRates = $this->fetchFromApi($url);

        if ($exchangeRates && isset($exchangeRates['rates'])) {
            // Save the exchange rates in a cookie for 5 minutes
            setcookie("exchange_rates_{$currency}", json_encode($exchangeRates['rates']), time() + $this->cookieExpiry);
            return $exchangeRates['rates'];
        }

        throw new Exception("Unable to fetch exchange rates for {$currency}.");
    }

    // Fetch from API using cURL (same as before)
    private function fetchFromApi($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout after 10 seconds
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            return json_decode($response, true);
        }

        throw new Exception("API request failed with status code: $httpCode.");
    }

    // Other utility methods like clearing cookies remain unchanged...
}
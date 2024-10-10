<?php

namespace converter;

use Exception;

class converter
{
    private $apiUrl = 'https://api.exchangerate-api.com/v4/latest/';
    private $baseCurrency;
    private $targetCurrencies = [];
    private $amount;
    private $cookieExpiry;

    public function __construct($data = [])
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();  // Ensure session is started
        }

        $this->baseCurrency = isset($data['basecurrency']) ? strtoupper($data['basecurrency']) : 'USD';  // Set baseCurrency, default to 'USD'
        $this->cookieExpiry = isset($data['cookieexpiry']) ? $data['cookieexpiry'] : 300;  // Set cookieExpiry, default to 300 seconds
        $this->amount = isset($data['defaultamount']) && is_numeric($data['defaultamount']) && $data['defaultamount'] > 0 ? $data['defaultamount'] : 1;
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

    // Final method to execute the conversion
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

    // Private method to fetch exchange rates and store in sessions
    private function getExchangeRates($currency)
    {
        // Check if exchange rates are already saved in a session
        if (isset($_SESSION["exchange_rates_{$currency}"])) {
            $sessionData = $_SESSION["exchange_rates_{$currency}"];
            if (time() - $sessionData['timestamp'] < $this->cookieExpiry) {
                return $sessionData['rates'];
            } else {
                unset($_SESSION["exchange_rates_{$currency}"]); // Expire session data if it's too old
            }
        }

        // Fetch from API if not available in session or expired
        $url = $this->apiUrl . $currency;
        $exchangeRates = $this->fetchFromApi($url);

        if ($exchangeRates && isset($exchangeRates['rates'])) {
            // Save the exchange rates in a session for 5 minutes
            $_SESSION["exchange_rates_{$currency}"] = [
                'rates' => $exchangeRates['rates'],
                'timestamp' => time()
            ];
            return $exchangeRates['rates'];
        }

        throw new Exception("Unable to fetch exchange rates for {$currency}.");
    }

    // Fetch from API using cURL
    private function fetchFromApi($url)
    {
        $ch = curl_init();

        // Set headers (like Accept headers)
        $headers = [
            'Accept: application/json',
            'User-Agent: CurrencyConverterApp/1.0',  // Good practice to include a user agent
        ];

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $response = curl_exec($ch);

        // Capture HTTP status code and errors
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Handle any cURL errors
        if ($curlError) {
            throw new Exception("cURL error: " . $curlError);
        }

        // Handle HTTP status codes
        if ($httpCode >= 400) {
            throw new Exception("API request failed with HTTP status code: $httpCode");
        }

        // Parse the JSON response
        $decodedResponse = json_decode($response, true);

        // Check if the JSON decoding was successful
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Failed to parse API response: " . json_last_error_msg());
        }

        return $decodedResponse;
    }


    // Chainable methods for baseCurrency, expiry, and defaultAmount
    public function baseCurrency(string $currency)
    {
        $this->baseCurrency = strtoupper($currency);  // Ensure it's uppercase
        return $this;
    }

    public function expiry(int $seconds)
    {
        $this->cookieExpiry = $seconds;
        return $this;
    }

    public function defaultAmount(int $amount)
    {
        if ($amount <= 0) {
            throw new Exception("Amount must be a positive number.");
        }
        $this->amount = $amount;
        return $this;
    }
}
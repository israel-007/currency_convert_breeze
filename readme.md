# Currency Converter Breeze

This currency conversion library is a simple, chainable PHP library that provides easy access to real-time currency conversion using exchange rates fetched from an external API `(https://api.exchangerate-api.com)`. It allows you to set a base currency, specify target currencies, and convert amounts with just a few lines of code. This library handles caching exchange rates in the session for efficiency and provides both array and JSON output formats for flexibility.

## Key Features:

* Chainable methods: Set the base currency, target currencies, and amount in a fluid, intuitive way.
* Supports multiple currency conversions in a single call.
* Flexible output: Get results as an array or JSON.
* Error handling: Provides informative error messages and status responses.
* Session-based caching to minimize API requests.

## Installation

You can install the library via Composer. Run the following command in your terminal:
```bash
composer require breeze_converter/currency
```
Make sure Composer is installed on your system. For more information on Composer, visit the [Composer Documentation](https://getcomposer.org/).

## Getting Started

Follow the steps below to start using the Converter library in your PHP project:

### 1. Basic Usage Example

Here's a quick example to convert an amount from one currency to multiple target currencies:
```php
require 'vendor/autoload.php';

use converter\Converter;

try {
    $converter = new Converter([
        'basecurrency' => 'USD',  // Optional: default is 'USD'
        'defaultamount' => 100,   // Optional: default is 1
        'cookieexpiry' => 300     // Optional: caching duration in seconds
    ]);

    $result = $converter
        ->from('USD')                   // Set base currency
        ->to(['EUR', 'GBP', 'CAD'])     // Set target currencies
        ->amount(100)                   // Set amount to convert
        ->run('json');                  // Get result in JSON format

    echo $result;  // Output converted values as JSON

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
```

### 2. Session Handling

Ensure that PHP sessions are enabled as the library uses session-based caching for exchange rates.

## Responses

The Converter library returns data in two formats: Array or JSON, depending on the output type you specify when calling the run() method.

### Successful Response
If the conversion is successful, you'll receive a response with the status "success" and the converted values.

> Example (Array Format):
```php
$response = $converter
    ->from('USD')
    ->to(['EUR', 'GBP'])
    ->amount(100)
    ->run('array');

print_r($response);
```

> Response:
```php
$Array
(
    [status] => success
    [values] => Array
        (
            [EUR] => 93.5
            [GBP] => 83.2
        )
)
```

> Example (JSON Format):

```php
$response = $converter
    ->from('USD')
    ->to(['EUR', 'GBP'])
    ->amount(100)
    ->run('json');

echo $response;
```

> esponse

```php
{
    "status": "success",
    "values": {
        "EUR": 93.5,
        "GBP": 83.2
    }
}
```

### Error Response
If an error occurs (e.g., invalid currency code, API failure), the library will return a response with the status `"error"` and an error message.

> Example:
```php
$response = $converter
    ->from('USD')
    ->to(['INVALID'])
    ->run('array');

print_r($response);
```

> Response:
```php
Array
(
    [status] => error
    [message] => "Currency conversion rate for INVALID not available."
)
```

> [!NOTE]
> With just a few lines of code, you can convert amounts between multiple currencies, making this library ideal for e-commerce platforms, financial applications, and any service that requires currency conversion!

## Default Settings

The library provides a set of default values that can be used for ease of setup, while also allowing flexibility to override these settings using chainable methods.

1. Base Currency
* Default: `'USD'`
* By default, the library uses the US Dollar (`USD`) as the base currency for conversion. This means that unless specified otherwise, all conversions will be calculated from USD.
* How to Overwrite: Use the `baseCurrency()` method to specify a different base currency. For example, if you want to use Canadian Dollars (`CAD`) as the base currency:

```php
$converter->baseCurrency('CAD');  // Sets the base currency to CAD
```

2. Default Amount
* Default: `1`
* If no specific amount is provided, the library defaults to converting `1` unit of the base currency to the target currencies. This is useful for quick rate checks.
* How to Overwrite: You can set a custom amount using the `defaultAmount()` method. For instance, to convert 100 CAD:

```php
$converter->defaultAmount(100);  // Sets the amount to 100 units of the default currency
```

3. Cookie Expiry (Session Cache)
* Default: `300` seconds (5 minutes)
* The library caches exchange rates in the session for 5 minutes by default. This reduces the number of API requests, improving efficiency. Cached rates will be used within the specified expiry time, after which a new API request will be made.
* How to Overwrite: You can customize the caching time using the `expiry()` method. For example, to keep the cached rates for 5 minutes:

```php
$converter->expiry(300);  // Set cache expiry time to 300 seconds (5 minutes)
```
* Impact of Reducing the Cookie Expiry Time: Reducing the expiry time means that the cached exchange rates will expire faster, leading to more frequent API calls. This can be useful if you need more up-to-date rates but can also lead to slower performance and hitting API rate limits. Conversely, increasing the expiry time (e.g., to 30 minutes) will reduce the number of API calls but may result in using outdated rates during that period.

> Example Usage with Custom Default Settings:
```php
$converter = new Converter();

// Set base currency to CAD, default amount to 100, and cache expiry to 5 minutes
$converter->baseCurrency('CAD')
          ->defaultAmount(100)
          ->expiry(300);

// Convert from CAD to NGN, GBP, and USD, and get the result in JSON format
$result = $converter->to(['NGN', 'GBP', 'USD'])->run('json');

echo $result;
```

In this example:

* The base currency is set to `CAD` instead of the default `USD`.
* The amount to convert is set to `100` instead of the default `1`.
* The session cache expiry is set to `300` seconds (5 minutes).

#

## Contributing
Contributions are welcomed to this library Whether you're fixing bugs, adding new features, improving documentation, or suggesting enhancements, your contributions are valuable.

Feel free to submit pull requests or open issues if you encounter any problems or have suggestions for improvement.

## Dependencies
This project does not rely on any external dependencies, making it easy to set up and use.

## License

This project is licensed under the [MIT License](LICENSE).


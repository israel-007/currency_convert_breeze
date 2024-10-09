# Currency Converter Breeze

This PHP Currency Conversion Library allows you to easily convert between different currencies using real-time exchange rates from a specified API. The library is fully customizable, offering chainable methods to set the base currency, target currencies, and the amount to be converted. It also supports caching exchange rates in cookies for improved efficiency.

## Key Features:

* Chainable methods: Set the base currency, target currencies, and amount in a fluid, intuitive way.
* Supports multiple currency conversions in a single call.
* Flexible output: Get results as an array or JSON.
* Error handling: Provides informative error messages and status responses.
* Caches exchange rates in cookies for quicker lookups.

## Usage

```php

$converter = new Converter();
$result = $converter->from('USD')
                    ->to(['EUR', 'GBP', 'JPY'])
                    ->amount(100)
                    ->run();
print_r($result);

```

With just a few lines of code, you can convert amounts between multiple currencies, making this library ideal for e-commerce platforms, financial applications, and any service that requires currency conversion!

> [!NOTE]
> Under Development

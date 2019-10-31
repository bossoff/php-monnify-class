# php-monnify-class
Test by RelianceHMO

## Installation
* `git clone https://github.com/djunehor/php-monnify-class.git`
* `cd php-monnify-class`
* `composer install`

## Usage
```
<?php
use Djunehor\Monnify\Monnify;

$monify = new Monnify($username, $password, $contractCode);

//if you'll be accessing authenticated end point
$monify->authenticate();

//get refreshed token
$monify->getRefreshToken();

?>
```

## Test
`./vendor/bin/phpunit test`

## Challenges
* The API kept failing when calling calling the reserve and unreserve endpoint even though I'm using the sample payload in the docs.

[![Build Status](https://travis-ci.org/alceanicu/cnp.svg?branch=master)](https://travis-ci.org/alceanicu/cnp) [![Latest Stable Version](https://poser.pugx.org/alcea/cnp/v/stable.svg)](https://packagist.org/packages/alcea/cnp) [![Total Downloads](https://poser.pugx.org/alcea/cnp/downloads.svg)](https://packagist.org/packages/alcea/cnp) [![License](https://poser.pugx.org/alcea/cnp/license.svg)](https://packagist.org/packages/alcea/cnp)

# CNP
Validation for Romanian Social Security Number (Validare CNP).

From version 2.0 was added possibility to extract some data from a valid CNP (see example below).

## How to install?

### 1. Use composer
```php
composer require alcea/cnp
```

### 2. or, edit require section from composer.json and run composer update
```
"alcea/cnp": "^2.1"
```

## How to use?

```php
<?php
// require __DIR__ . '\vendor\autoload.php';
use alcea\cnp\Cnp;

$cnpToBeValidated = '5110102441483';

echo "CNP {$cnpToBeValidated} is " . Cnp::validate($cnpToBeValidated) ? 'valid' : 'invalid';

// OR 

$cnp = new Cnp($cnpToBeValidated);
if ($cnp->isValid()) {
    // extract information from CNP
    echo "CNP {$cnpToBeValidated} - is valid" . PHP_EOL;
    echo "Birth Date: {$cnp->getBirthDateFromCNP('Y/m/d')}" . PHP_EOL;
    echo "Birth Place: {$cnp->getBirthCountyFromCNP()}" . PHP_EOL;
    echo "Gender: {$cnp->getGenderFromCNP('male', 'female')}" . PHP_EOL;
    echo "Serial: {$cnp->getSerialNumberFromCNP()}" . PHP_EOL; 
    echo "Person is " . ($cnp->isPersonMajor() ? '' : 'not' ) . ' major' . PHP_EOL;
    echo "Person have an Identity Card " . ($cnp->hasIdentityCard() ? 'YES' : 'NO' );
} else {
    echo "CNP {$cnpToBeValidated} is invalid" . PHP_EOL;
}
```

## How to run tests?
```
## Open an terminal and run commands:
git clone https://github.com/alceanicu/cnp.git
cd cnp
composer install
./vendor/bin/phpunit --bootstrap ./vendor/autoload.php --testdox
```

## License

This package is licensed under the [MIT](http://opensource.org/licenses/MIT) license.

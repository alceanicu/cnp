[![Build Status](https://travis-ci.org/alceanicu/cnp.svg?branch=master)](https://travis-ci.org/alceanicu/cnp) [![Latest Stable Version](https://poser.pugx.org/alcea/cnp/v/stable.svg)](https://packagist.org/packages/alcea/cnp) [![Total Downloads](https://poser.pugx.org/alcea/cnp/downloads.svg)](https://packagist.org/packages/alcea/cnp) [![License](https://poser.pugx.org/alcea/cnp/license.svg)](https://packagist.org/packages/alcea/cnp)

# CNP
PHP validation for Romanian Social Security Number (Validare CNP)

#How to install?

### 1. Use composer
```php
composer require  alcea/cnp "~1"
```

### 2. or, edit require section from composer.json
```
"alcea/cnp": "~2"
```

### 3. or, clone from GitHub
```
git clone https://github.com/alceanicu/cnp.git
```

#How to use?

```php
<?php
use alcea\cnp\Cnp;

$cnpToBeValidated = '5110102441483';
$cnp = new Cnp($cnpToBeValidated);
if ($cnp->isValid()) {
    // get info from CNP
    echo "CNP {$cnpToBeValidated} - is valid" . PHP_EOL;
    echo "Birth Date: {$cnp->getBirthDateFromCNP('Y/m/d')}" . PHP_EOL;
    echo "Birth Place: {$cnp->getBirthCountyFromCNP()}" . PHP_EOL;
    echo "Gender: {$cnp->getGenderFromCNP('male', 'female')}" . PHP_EOL;
    echo "Serial: {$cnp->getSerialNumberFromCNP()}" . PHP_EOL;
} else {
    echo "CNP {$cnpToBeValidated} is invalid" . PHP_EOL;
}
?>
```

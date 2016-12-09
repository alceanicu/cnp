[![Build Status](https://travis-ci.org/alceanicu/Cnp.svg?branch=master)](https://travis-ci.org/alceanicu/Cnp) [![Latest Stable Version](https://poser.pugx.org/alcea/cnp/v/stable.svg)](https://packagist.org/packages/alcea/cnp) [![Total Downloads](https://poser.pugx.org/alcea/cnp/downloads.svg)](https://packagist.org/packages/alcea/cnp) [![Latest Unstable Version](https://poser.pugx.org/alcea/cnp/v/unstable.svg)](https://packagist.org/packages/alcea/cnp) [![License](https://poser.pugx.org/alcea/cnp/license.svg)](https://packagist.org/packages/alcea/cnp)

# CNP
PHP - Validation for Romanian Social Security Number (Validare CNP)

#How to use?

```php
<?php

$cnpToValidate = 'xxx';
$cnp = new \Cnp\Cnp($cnpToValidate);
if ($cnp->isValid()) {
    echo "CNP {$cnpToValidate} - is valid" . PHP_EOL;
    echo "Birth Date: {$cnp->getBirthDateFromCNP('Y/m/d')}" . PHP_EOL;
    echo "Birth Place: {$cnp->getBirthCountyFromCNP()}" . PHP_EOL;
    echo "Gender: {$cnp->getGenderFromCNP('male', 'female')}" . PHP_EOL;
} else {
    echo "CNP {$cnpToValidate} is invalid" . PHP_EOL;
}
?>
```

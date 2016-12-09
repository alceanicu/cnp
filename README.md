# CNP
PHP - Validation for Romanian Social Security Number (Validare CNP)

```php
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
```

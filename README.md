[![Build Status](https://travis-ci.org/alceanicu/cnp.svg?branch=master)](https://travis-ci.org/alceanicu/cnp) [![Latest Stable Version](https://poser.pugx.org/alcea/cnp/v/stable.svg)](https://packagist.org/packages/alcea/cnp) [![Total Downloads](https://poser.pugx.org/alcea/cnp/downloads.svg)](https://packagist.org/packages/alcea/cnp) [![License](https://poser.pugx.org/alcea/cnp/license.svg)](https://packagist.org/packages/alcea/cnp)

# CNP
Validation for Romanian Social Security Number (Validare CNP).

From version 2.0 was added possibility to extract some data from a valid CNP (see example below).

## How to install?

### 1. Use composer
```php
composer require alcea/cnp
```

### 2. or, edit require section from composer.json
```
"alcea/cnp": "*"
```

### 3. or, clone from GitHub
```
git clone https://github.com/alceanicu/cnp.git
```

## How to use?

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
#### How to use with YII2?

```php
<?php
use yii\base\Model;
use alcea\cnp\Cnp;

/**
 * @see http://www.yiiframework.com/doc-2.0/guide-input-validation.html#creating-validators
 */
class MyForm extends Model
{
    public $cnp;
    
    public function rules()
    {
        return [
            ['cnp', function ($attribute, $params, $validator) {
                if (!(new Cnp($this->$attribute))->isValid()) {
                    $this->addError($attribute, 'CNP INVALID');
                }
            }]
        ];
    }
}
```

#### How to use with Laravel 5

- in app.php add: 'alcea\cnp\laravel\CnpValidatorProvider'

```php
<?php
public function rules()
{
    return [
        'cnp' => 'required|max:13|cnp',
    ];
}

// sau 

Validator::make($data, [
    'cnp' => 'required|max:13|cnp',
]);
```

# How to run tests?
```
## Open an terminal and run commands:
cd cnp
./vendor/bin/phpunit --bootstrap ./vendor/autoload.php --testdox
```


## License

This package is licensed under the MIT license. See [License File](LICENSE.md) for more information.

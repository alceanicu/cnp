<?php

namespace alcea\cnp\laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use alcea\cnp\Cnp;

/**
 * @deprecated since 2.1.3 and will be removed from 3.0.0
 */
class CnpValidatorProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        Validator::extend('cnp', function ($attribute, $value, $parameters) {
            return Cnp::validate($value);
        });

        Validator::replacer('cnp', function ($message, $attribute, $rule, $parameters) {
            return "CNP invalid!";
        });
    }

    /**
     * Register bindings in the container.
     * @return void
     */
    public function register()
    {
        // TODO
    }

}

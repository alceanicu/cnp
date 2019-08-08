<?php

namespace alcea\cnp\laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use alcea\cnp\Cnp;

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

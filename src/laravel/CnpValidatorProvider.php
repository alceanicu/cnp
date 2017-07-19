<?php

namespace alcea\cnp\laravel;

class CnpValidatorProvider extends Illuminate\Support\ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Illuminate\Support\Facades\Validator::extend('cnp', function($attribute, $value, $parameters) {
            return alcea\cnp\Cnp::valid($value);
        });

        Illuminate\Support\Facades\Validator::replacer('cnp', function($message, $attribute, $rule, $parameters) {
            $message = "CNP invalid!";

            return $message;
        });
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        // TODO
    }

}

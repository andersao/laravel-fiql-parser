<?php

namespace Prettus\Laravel\FIQL;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Illuminate\Database\Query\Builder as DatabaseBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Prettus\Laravel\FIQL\Query\FilterFIQL;

class FIQLServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        parent::register();
        DatabaseBuilder::macro('filter', function ($expression) {
            return with(new FilterFIQL($this, $expression))->apply();
        });
        EloquentBuilder::macro('filter', function ($expression) {
            return with(new FilterFIQL($this, $expression))->apply();
        });
    }
}

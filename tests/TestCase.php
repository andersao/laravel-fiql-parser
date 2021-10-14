<?php

namespace Tests;

use Prettus\Laravel\FIQL\FIQLServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            FIQLServiceProvider::class
        ];
    }
}

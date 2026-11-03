<?php

namespace SmartDato\AsendiaSync\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use SmartDato\AsendiaSync\AsendiaSyncServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelDataServiceProvider::class,
            AsendiaSyncServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('cache.default', 'array');
    }
}

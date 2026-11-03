<?php

namespace SmartDato\AsendiaSync;

use Illuminate\Support\Facades\Cache;
use SmartDato\AsendiaSync\Auth\AsendiaSyncAuthenticator;
use SmartDato\AsendiaSync\Connectors\AsendiaSyncConnector;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AsendiaSyncServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('asendia-sync-sdk')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(AsendiaSyncConnector::class, fn (): AsendiaSyncConnector => new AsendiaSyncConnector(
            asendiaSyncAuthenticator: new AsendiaSyncAuthenticator(
                username: (string) config('asendia-sync-sdk.username'),
                password: (string) config('asendia-sync-sdk.password'),
                cache: Cache::store(config('asendia-sync-sdk.cache_store')),
            ),
            baseUrl: config('asendia-sync-sdk.base_url') ?? AsendiaSyncConnector::PRODUCTION_URL,
        ));

        $this->app->singleton(AsendiaSync::class, fn ($app): AsendiaSync => new AsendiaSync(
            connector: $app->make(AsendiaSyncConnector::class),
            customerId: config('asendia-sync-sdk.customer_id'),
        ));
    }
}

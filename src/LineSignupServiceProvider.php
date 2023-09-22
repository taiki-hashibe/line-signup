<?php

namespace LowB\LineSignup;

use LowB\LineSignup\Commands\LineSignupCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LineSignupServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('line-signup')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_line-signup_table')
            ->hasCommand(LineSignupCommand::class);
    }
}

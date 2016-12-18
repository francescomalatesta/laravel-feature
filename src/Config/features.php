<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Scanned Paths
    |--------------------------------------------------------------------------
    |
    | You can easily add new features to the system with a simple call to the
    | FeatureManager::add() method. However, as a bonus, you can also use
    | the artisan feature:scan command.
    |
    | It will search for every view in resources/views path and add new
    | features to the system starting from the @feature() directives in views.
    |
    | If you have your views in other directories, you can change this array
    | in the way you need. All the paths in this array will be scanned for
    | new features.
    |
    */

    'scanned_paths' => [
        base_path('resources/views')
    ],

    /*
    |--------------------------------------------------------------------------
    | Scanned Features Default Status
    |--------------------------------------------------------------------------
    |
    | When you use the feature:scan command, new features could be added to the
    | system. Be default, this new features are disabled. You can change this
    | by setting this value to true instead of false.
    |
    | By doing so, new added features will be automatically enabled globally.
    |
    */

    'scanned_default_enabled' => true,

    /*
    |--------------------------------------------------------------------------
    | Features Repository
    |--------------------------------------------------------------------------
    |
    | Here you can configure the concrete class you will use to work with
    | features. By default, this class is the EloquentFeatureRepository shipped
    | with this package. As the name says, it works with Eloquent.
    |
    | However, you can use a custom feature repository if you want, just by
    | creating a new class that implements the FeatureRepositoryInterface.
    |
    */

    'repository' => LaravelFeature\Repository\EloquentFeatureRepository::class

];

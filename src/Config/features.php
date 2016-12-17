<?php

return [

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

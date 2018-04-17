# Laravel-Feature

[![Latest Stable Version](https://poser.pugx.org/francescomalatesta/laravel-feature/v/stable)](https://packagist.org/packages/francescomalatesta/laravel-feature)
[![Build Status](https://travis-ci.org/francescomalatesta/laravel-feature.svg?branch=master)](https://travis-ci.org/francescomalatesta/laravel-feature)
[![Code Coverage](https://scrutinizer-ci.com/g/francescomalatesta/laravel-feature/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/francescomalatesta/laravel-feature/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/francescomalatesta/laravel-feature/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/francescomalatesta/laravel-feature/?branch=master)
[![StyleCI](https://styleci.io/repos/76716509/shield?branch=master)](https://styleci.io/repos/76716509)

Laravel-Feature is a package fully dedicated to feature toggling in your application, in the easiest way. For Laravel, of course.

It was inspired by the [AdEspresso Feature Flag Bundle](https://github.com/adespresso/FeatureBundle).

## Feature-What?

Feature toggling is basically a way to **have full control on the activation of a feature** in your applications.

Let's make a couple of examples to give you an idea:

* you just finished to work on the latest feature and you want to push it, but the marketing team wants you to deploy it in a second moment;
* the new killer-feature is ready, but you want to enable it only for a specific set of users;

With Laravel-Feature, you can:

* easily **define new features** in your application;
* **enable/disable features** globally;
* **enable/disable features for specific users**, or **for whatever you want**;

There are many things to know about feature toggling: take a look to [this great article](http://martinfowler.com/articles/feature-toggles.html) for more info. It's a really nice and useful lecture.

## Install

You can install Laravel-Feature with Composer.

``` bash
$ composer require francescomalatesta/laravel-feature
```

After that, you need to **add the `FeatureServiceProvider` to the `app.php` config file**.

```php
...
LaravelFeature\Provider\FeatureServiceProvider::class,
...
```

Now you have to **run migrations**, to add the tables Laravel-Feature needs.

```bash
$ php artisan migrate
```

... and you're good to go!

### Facade

If you want, you can also **add the `Feature` facade** to the `aliases` array in the `app.php` config file.

```php
...
'Feature' => \LaravelFeature\Facade\Feature::class,
...
```

If you don't like Facades, **inject the `FeatureManager`** class wherever you want!

### Config File

By default, you can immediately use Laravel-Feature. However, if you want to tweak some settings, feel free to **publish the config file** with

```bash
$ php artisan vendor:publish --provider="LaravelFeature\Provider\FeatureServiceProvider"
```

## Basic Usage

There are two ways you can use features: working with them **globally** or **specifically for a specific entity**.

### Globally Enabled/Disabled Features

#### Declare a New Feature

Let's say you have a new feature that you want to keep hidden until a certain moment. We will call it "page_code_cleaner". Let's **add it to our application**:

```php
Feature::add('page_code_cleaner', false);
```

Easy, huh? As you can imagine, **the first argument is the feature name**. **The second is a boolean we specify to define the current status** of the feature.

* `true` stands for **the feature is enabled for everyone**;
* `false` stands for **the feature is hidden, no one can use it/see it**;

And that's all.

#### Check if a Feature is Enabled

Now, let's imagine a better context for our example. We're building a CMS, and our "page_code_cleaner" is used to... clean our HTML code. Let's assume we have a controller like this one.

```php
class CMSController extends Controller {
    public function getPage($pageSlug) {
        
        // here we are getting our page code from some service
        $content = PageService::getContentBySlug($pageSlug);
        
        // here we are showing our page code
        return view('layout.pages', compact('content'));
    }
}
```

Now, we want to deploy the new service, but **we don't want to make it available for users**, because the marketing team asked us to release it the next week. LaravelFeature helps us with this:

```php
class CMSController extends Controller {
    public function getPage($pageSlug) {
        
        // here we are getting our page code from some service
        $content = PageService::getContentBySlug($pageSlug);
        
        // feature flagging here!
        if(Feature::isEnabled('page_code_cleaner')) {
            $content = PageCleanerService::clean($content);
        }
        
        // here we are showing our page code
        return view('layout.pages', compact('content'));
    }
}
```

Ta-dah! Now, **the specific service code will be executed only if the "page_code_cleaner" feature is enabled**.

#### Change a Feature Activation Status

Obviously, using the `Feature` class we can easily **toggle the feature activation status**.

```php
// release the feature!
Feature::enable('page_code_cleaner');

// hide the feature!
Feature::disable('page_code_cleaner');
```

#### Remove a Feature

Even if it's not so used, you can also **delete a feature** easily with

```php
Feature::remove('page_code_cleaner');
```

Warning: *be sure about what you do. If you remove a feature from the system, you will stumble upon exceptions if checks for the deleted features are still present in the codebase.*

#### Work with Views

I really love blade directives, they help me writing more elegant code. I prepared **a custom blade directive, `@feature`**:

```php
<div>This is an example template div. Always visible.</div>

@feature('my_awesome_feature')
    <p>This paragraph will be visible only if "my_awesome_feature" is enabled!</p>
@endfeature

<div>This is another example template div. Always visible too.</div>
```

A really nice shortcut!

### Enable/Disable Features for Specific Users/Entities

Even if the previous things we saw are useful, LaravelFeature **is not just about pushing the on/off button on a feature**. Sometimes, business necessities require more flexibility. Think about a [**Canary Release**](http://martinfowler.com/bliki/CanaryRelease.html): we want to rollout a feature only to specific users. Or, maybe, just for one tester user.

#### Enable Features Management for Specific Users

LaravelFeature makes this possible, and also easier just as **adding a trait to our `User` class**.

In fact, all you need to do is to: 

* **add the `LaravelFeature\Featurable\Featurable` trait** to the `User` class;
* let the same class **implement the `FeaturableInterface` interface**;

```php
...

class User extends Authenticatable implements FeaturableInterface
{
    use Notifiable, Featurable;
    
...
```

Nothing more! LaravelFeature now already knows what to do.

#### Status Priority

*Please keep in mind that all you're going to read from now is not valid if a feature is already enabled globally. To activate a feature for specific users, you first need to disable it.*

Laravel-Feature **first checks if the feature is enabled globally, then it goes down at entity-level**.

#### Enable/Disable a Feature for a Specific User

```php
$user = Auth::user();

// now, the feature "my.feature" is enabled ONLY for $user!
Feature::enableFor('my.feature', $user);

// now, the feature "my.feature" is disabled for $user!
Feature::disableFor('my.feature', $user);

```

#### Check if a Feature is Enabled for a Specific User

```php
$user = Auth::user();

if(Feature::isEnabledFor('my.feature', $user)) {
    
    // do amazing things!
    
}
```

#### Other Notes

LaravelFeature also provides a Blade directive to check if a feature is enabled for a specific user. You can use the `@featurefor` blade tags:
```php
@featurefor('my.feature', $user)
    
    // do $user related things here!
    
@endfeaturefor
```

## Advanced Things

Ok, now that we got the basics, let's raise the bar!

### Enable Features Management for Other Entities

As I told before, you can easily add features management for Users just by using the `Featurable` trait and implementing the `FeaturableInterface` in the User model. However, when structuring the relationships, I decided to implement a **many-to-many polymorphic relationship**. This means that you can **add feature management to any model**!

Let's make an example: imagine that **you have a `Role` model** you use to implement a basic roles systems for your users. This because you have admins and normal users. 

So, **you rolled out the amazing killer feature but you want to enable it only for admins**. How to do this? Easy. Recap:

* add the `Featurable` trait to the `Role` model;
* be sure the `Role` model implements the `FeaturableInterface`;

Let's think the role-user relationship as one-to-many one.

You will probably have a `role()` method on your `User` class, right? Good. You already know the rest:

```php
// $role is the admin role!
$role = Auth::user()->role;

...

Feature::enableFor('my.feature', $role);

...

if(Feature::isEnabledFor('my.feature', $role)) {

    // this code will be executed only if the user is an admin!
    
}
```

### Scan Directories for Features

One of the nice bonuses of the package that inspired me when making this package, is the ability to **"scan" views, find `@feature` declarations and then add these scanned features if not already present** on the system.

I created a simple **artisan command** to do this.

```bash
$ php artisan feature:scan
```

The command will use a dedicated service to **fetch the `resources/views` folder and scan every single Blade view to find `@feature` directives**. It will then output the search results.

Try it, you will like it!

**Note:** if you have published the config file, you will be able to **change the list of scanned directories**.

### Using a Custom Features Repository

Imagine that you want to **change the place or the way you store features**. For some crazy reason, you want to store it on a static file, or on Dropbox. 

Now, Eloquent doesn't have a Dropbox driver, so you can't use this package. **Bye.**

Just joking! When making this package, I wanted to be sure to create a fully reusable logic if the developer doesn't want to use Eloquent anymore.

To do this, I created a nice interface for the Job, and created some bindings in the Laravel Service Container. Nothing really complex, anyway.

The interface I am talking about is `FeatureRepositoryInterface`.

```php
<?php

namespace LaravelFeature\Domain\Repository;

use LaravelFeature\Domain\Model\Feature;
use LaravelFeature\Featurable\FeaturableInterface;

interface FeatureRepositoryInterface
{
    public function save(Feature $feature);

    public function remove(Feature $feature);

    public function findByName($featureName);

    public function enableFor($featureName, FeaturableInterface $featurable);

    public function disableFor($featureName, FeaturableInterface $featurable);

    public function isEnabledFor($featureName, FeaturableInterface $featurable);
}
```

This interface has some methods. Let's quickly explain them:

* **save**: this method is used to save a new feature on the system if not already present;
* **remove**: this method is used to remove a feature from the system;
* **findByName**: this method is used to find a feature from the system, given its name;
* **enableFor**: used to enable a feature for a specific `Featurable` entity;
* **disableFor**: used to disable a feature for a specific `Featurable` entity;
* **isEnabledFor**: used to check if a feature is enabled for a specific `Featurable` entity;

So, you will need to **create a new `DropboxFeatureRepository` that implements `FeatureRepositoryInterface`**, with all the methods you just saw.

Finally, you will have to change the repository binding in the `features.php` config file:

```php
'repository' => LaravelFeature\Repository\EloquentFeatureRepository::class
```

will become...

```php
'repository' => My\Wonderful\DropboxFeatureRepository::class
```

Done! By the way, don't forget to let the entities you need to **implement the `FeaturableInterface`**.

```php
<?php

namespace LaravelFeature\Featurable;

interface FeaturableInterface
{
    public function hasFeature($featureName);
}
```

The only method, here, is `hasFeature`. It's used to define if the given feature is enabled for that entity, or not.

To have a better idea of the mechanism, feel free to look to the `EloquentFeatureRepository` class and the `Featurable` trait I built for the Eloquent implementation.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

You can start tests with a simple:

``` bash
$ phpunit
```

or, also

```bash
$ composer test
```

There are two separate test suites:

* Unit;
* Integration;

``` bash
$ phpunit --testsuite=unit
$ phpunit --testsuite=integration
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email francescomalatesta@live.it instead of using the issue tracker.

## Credits

* [Francesco Malatesta](https://github.com/francescomalatesta)
* [All Contributors](https://github.com/francescomalatesta/laravel-feature/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

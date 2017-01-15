# Laravel Handy Request

This module makes easier to filter all incoming requests in **Laravel 5** applications. For example, let's assume you want to trim all incoming data or maybe add some extra fields that don't come from external input - this all you can do with a few steps to work in your whole application.
 
## Installation

### Basic installation
 
```php
composer require mnabialek/laravel-handy-request
``` 

in console to install this module. This is all you need, however depending on your requirements you might need to do some extra steps (see **Advanced Installation**)

### Advanced Installation

The default way of using this package is modifying input before validation. The same modified input will be used later after validation. However in some cases you might not want to use validation at all or you want to modify input globally for the whole application and you want to still use default Request iOC binding instead of creating custom ones. 

In case you want to apply some filters globally and not only for specific routes you need to do some additional changes. Let's assume you will create your custom Request class where you want to modify your input globally (we will discuss it later) and you can access this file using the following namespace and name `App\Http\Requests\MyRequest`. In such case, to use your custom request globally 

1) Open your `index.php` (by default in `public` directory) and add:

```php
App\Http\Requests\MyRequest::replaceDefaultRequest($app);
```

just after
 
 ```php
 $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
 ```
 
 to make sure your custom Request class will be used later in your application
 
2) In case you use tests for your app (what you should definitely do) you should also make additional change to make similar change your tests. You need to open `tests/TestCase.php` file and in `createApplication` method you should add:

```php
\App\Http\Requests\MyRequest::replaceDefaultRequest($app);
```

just before

```php
$kernel->bootstrap();
```

Making those above 2 steps will allow you to use your custom Request class in the whole application and you refer in your app into Request using `\Illuminate\Http\Request` dependency injection or `request` container binding ($app['request']) without a problem.
 
## Accessing original and filtered input

The core file that holds the logic is `\Mnabialek\LaravelHandyRequest\Traits\HandyRequest` trait. In your real application you might want to create your custom trait that will extend this trait which allow you easier making any custom changes you would like to make in future.

By default all usages of `input` will return input **after** applying all filters you want to apply. However you can use `original` method to access original input (without filters) or use `filtered` method if you want to be more explicit to access filtered input. 

By default all usages of `input`, `all`, `only`, `except` etc method will return filtered input. However if you want them to return original input, in your trait that extends original trait, you can just override `input` method like this:

```php
public function input($key = null, $default = null)
{
   return $this->original($key, $default);
}
```

## General information about filtering

In order to filter input you need to apply trait mentioned in previous paragraph (original or your custom one) in request class you want to use filtering input like this. 

```php
use \Mnabialek\LaravelHandyRequest\Traits\HandyRequest;
```

and also define filters you want to use.

In case you need to use global filtering (mentioned in **Advanced installation**) there is also created `\Mnabialek\LaravelHandyRequest\HandyRequest` class that you should only extend with your custom global request class (and define filters in your class)

## Using filters

### Defining applied filters

In order to apply any filters you should define `$filters` property in your class and define in it filters you want to use. Filters are run in the same order they are defined. For each filter you can only specify filter name or filter name together with fields you want to include or exclude.

For example:

```php
protected $filters = [
   'trim',
   'checkbox' => [
       'only' => ['terms', 'checkbox'],
       'value' => false,
    ],
    'nullable' => [
       'except' => ['terms', 'active'] 
    ]    
];
```
will cause that:
- `trim` filter will be used for the whole input
- `checkbox` filter will be used only for `terms` and `checkbox` fields and in addition `value` option will be passed into this filter with `false` value.
- `nullable` filter will be used for all fields except `terms` and `active` fields

For specifying field names you can use explicit field names or you can use fields constraints (see **Fields constraints** for details).

### Modifying the whole input
 
Sometimes for various reasons you might need to modify the whole input. For example you might want to remove some field from input or add another. You can do it using custom filters or methods for fields, but sometimes it will be impossible without having access to the whole input. In such case you can define `modifyInput` method that will allow you to make such change.

For example

```php
protected function modifyInput(array $input) 
{
   $input['test'] = ' abc';
   unset($input['terms']);
   
   return $input;
}
```

will cause that you add new field `test` with value ` abc` and remove `terms` field from input completely.

**Be aware that those modified input will be affected by any filters defined**. So in above case if you had defined `trim` filter finally you will get `abc` value for `test` field instead of `abc` preceded by space. In case you want to exclude such field from applying filter, you need to use `except` option for selected filters with such field/

### Custom fields filters

In some cases you might want to add your own logic for selected field. In order to do this you need to create function with `applySampleFieldFilter` name where `Sample` here corresponds to `sample` field in input. Be aware it won't work for more complex fields (as for example nested arrays). In such case if you want to define custom filter method for selected field, you need to add `fieldFiltersMethods` property with your custom method for each field. For example it could look like this:
  
```php
protected $fieldFiltersMethods = [
   'name' => 'filterName',
   'address.*' => 'filterAllAddressFields',
];
```

As you see here we defined custom method for `name` field and for `address.*` field (see **Fields constraints** for details).

No matter if you use default field filter function name or you want to define custom one, you need to define it like this:

```php
protected function filterName($value, $fullKey) 
{
  return 'modified '.$value; 
}
```

for this function you get value of field and also full key of field (in dot notation). You can obviously use `$fullKey` to add any logic, for example if for `address` you want to make change for all address field except `street` you can define then `filterAllAddressFields` like so:

```php
protected function filterAllAddressFields($value, $fullKey) 
{
  if ($fullKey == 'address.street') {
     return $value;
  }
  return 'modified '.$value; 
}
``` 

Be aware when defining custom filter methods any filters for this fields won't be applied. In case you want to apply filters for this field in your custom method you need to run:

```php
$value = $this->applyFilters($value, $fullKey, $this->normalizedFilters());
```

to get value filtered by other filters and now you can add any custom transformations for this value.

### Fields constraints

For both `filters` and `fieldFilterMethods` you can use field constraints. It means you can use not only explicit field name, but you can also define nested fields (using dot notation), or all subfields of given field. For example:
 
- `name` - will match `name` field. But in case you have also `name` field for `address` (so it's `name.address`) it won't be matched
- `address.name` - will match `name` field in `address`
- `people.0.name` - will match `name` field of 1st person
- `address.*` - will match all children of `address` field. However it won't match any further descendant fields. For example it will match `address.name` field but it won't match `address.description.excerpt` field  
- `address.**` - will match all descendants of `address` field. For example it will match `address.name` but it will also match `address.description.excerpt` field. You should use `**` only at the end of field constraint

## Filters

### Type of filters

In general there are 2 types of filters:

- `field filters` - they operate on single field value
- `global filters` - the operate on the whole input. They might be useful if you need to make some operations based on other fields or conditionally add something to input (or modify input) based on the whole input

### Available filters

At the moment are available the following filters. If you create any filter and you think it will be useful for other people, please create Pull request with this (ideally with unit tests for this filter).

#### trim

It will trim string field
 
#### nullable

It will convert any empty value into `null`. Be aware this filter doesn't trim input so if you send empty space as value and don't use trim filter, it won't be convert into `null` value
 
#### website
 
It will automatically add `http://` into url field in case it's not empty and it doesn't start with `http://`  or `https://`. It doesn't trim the value so you should probably use `trim` filter too. 

Available options:

- `secure` - By default set to `false`. if you set it to value converting to `true` it will add `https://` if needed instead of `http://`

#### secure_website

The same as `website` filter, but `secure` by default is set to `true`

#### checkbox

This is global filter. It will automatically set value to `0` in case field doesn't exist in input. Be aware it won't work for complex field constraints (using `*` or `**`) - but it will work for field in dot notation. This filter needs also to be specified field in `only` option (it won't work for `except` option).

Available options:

- `value` - if you set it to any value, this value will be automatically set to to added key. By default it's set to `0`

## Defining custom filters

Sometimes built-in filters won't be enough or you want to override built-in filters with your custom ones to better fit your needs. In such case you can create your own filter class and need to register it. What you need is to define `registerFilters` method in your request class you are using to do that and call in it `registerFilter` method to register any custom filter. It could look for example like this:

```php
protected function registerFilters()
{
   static::registerFilter('trim', \App\RequestFilters\MyCustomTrimFilter::class);
   static::registerFilter('new_filter', \App\RequestFilters\BrandNewFilter::class);
}
```

Obviously depending on your setup and chosen solutions, you might define such filters also in `AppServiceProvider` or extend some Request classes for base class in where you might define common filters for all other request classes.

## Contributions

All contributions are welcome. Especially if you want to add new filter that you think will be useful for many developers, you can propose this filter creating new **Pull request**

## Licence

This package is licenced under the [MIT license](http://opensource.org/licenses/MIT)
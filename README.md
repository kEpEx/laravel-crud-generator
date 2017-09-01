# laravel-crud-generator

[![Latest Stable Version](https://poser.pugx.org/kepex/laravel-crud-generator/v/stable)](https://packagist.org/packages/kepex/laravel-crud-generator) [![Total Downloads](https://poser.pugx.org/kepex/laravel-crud-generator/downloads)](https://packagist.org/packages/kepex/laravel-crud-generator) [![Latest Unstable Version](https://poser.pugx.org/kepex/laravel-crud-generator/v/unstable)](https://packagist.org/packages/kepex/laravel-crud-generator) [![License](https://poser.pugx.org/kepex/laravel-crud-generator/license)](https://packagist.org/packages/kepex/laravel-crud-generator)

php artisan command to generate fully working crud with grid paginated server side only by having database tables


### Installing
```
php composer.phar require kepex/laravel-crud-generator
```

Add to config/app.php the following line to the 'providers' array:
```
CrudGenerator\CrudGeneratorServiceProvider::class,
```

![Preview](https://raw.githubusercontent.com/kEpEx/laravel-crud-generator/master/preview.gif)


### Usage

Use the desired model name as the input 


CRUD for students table
```
php artisan make:crud student
```
or the whole database
```
php artisan make:crud all
```
whole database with custom layout
```
php artisan make:crud all --master-layout=layouts.master 
```
Because sometimes you need boilerplate code only for view and controller, you can use an existing model with custom controller name
```
php artisan make:crud student --master-layout=master --custom-controller=dashboard	
```
For more options 
```
php artisan help make:crud
```
### Custom Templates

The best power of this plugin relies on you making your own templates and generating the code the way you like

Run this command:
```
php artisan vendor:publish
```
and you will have now in resources/templates/ the files you need to modify

If you want to go back to the default, just delete them

Let me know if you have any questions or if you find this library useful at twitter @[kEpEx](https://twitter.com/kepex)

# laravel-crud-generator


php artisan command to generate fully working crud with grid paginated server side only by having database tables


###Installing

	php composer.phar require kepex/laravel-crud-generator dev-master


Add to config/app.php the following line to the 'providers' array:

    CrudGenerator\CrudGeneratorServiceProvider::class,


![Preview](https://raw.githubusercontent.com/kEpEx/laravel-crud-generator/master/preview.gif)


###Usage


CRUD for students table

	php artisan make:crud students

or the whole database

	php artisan make:crud all

whole database with custom layout

	php artisan make:crud all "" layouts.master 	
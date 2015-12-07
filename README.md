# laravel-catalogue-maker
Generate model, controller with templates that provides basic functionality for listing, adding and modifying entities

Installing

php composer.phar require kepex/laravel-catalogue-maker dev-master


Add to config/app.php the following line to the 'providers' array:

    CatalogueMaker\CatalogueMakerServiceProvider::class,


Usage

	php artisan make:catalogue database_table_name
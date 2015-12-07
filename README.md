# laravel-crud-generator

###*Still on development*

php artisan command to generate fully working crud with grid paginated server side only by having database tables


Installing

php composer.phar require kepex/laravel-crud-generator dev-master


Add to config/app.php the following line to the 'providers' array:

    CrudGenerator\CrudGeneratorServiceProvider::class,


Usage

	php artisan make:crud database_table_name
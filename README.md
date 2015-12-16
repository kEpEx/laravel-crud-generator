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

###Custom Templates

The best power of this plugin relies on you making your own templates and generating the code the way you like

Run this command:

    php artisan vendor:publish

and you will have now in resources/templates/ the files you need to modify

If you want to go back to the default, just delete them

Let me know if you have any questions at twitter @[kEpEx](https://twitter.com/kepex)
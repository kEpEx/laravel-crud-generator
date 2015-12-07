<?php

namespace CatalogueMaker\Console\Commands;

//namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Artisan;

class CatalogueMakerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:catalogue {name} {--singular} {--recreate} {custom_table_name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create catalogues based on a mysql table instantly';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tablename = strtolower($this->argument('name'));
        $tablename_plural = str_plural($tablename);
        $prefix = \Config::get('database.connections.mysql.prefix');

        $this->info('Creating catalogue for table: '.$tablename);
        $this->info('Model Name: '.ucfirst($tablename));
        
        if($this->option('recreate')) {
            $this->deletePreviousFiles($tablename);
        }

        $this->createModel($tablename, $prefix, $this->option('singular'), $this->argument('custom_table_name'));
        
        $modelfull = '\App\\'.ucfirst($tablename);
        $this->info('Example data: '.$modelfull::first());

        if(!is_dir(base_path().'/resources/views/'.$tablename_plural)) { 
            $this->info('Creating directory: '.base_path().'/resources/views/'.$tablename_plural);
            mkdir( base_path().'/resources/views/'.$tablename_plural ); 
        }
        
        $options = [
            'model_uc' => ucfirst($tablename),
            'model_singular' => $tablename,
            'model_plural' => $tablename_plural,
            'tablename' => $this->option('singular') ? str_singular($tablename) : ($this->argument('custom_table_name') ?: $tablename),
            'prefix' => $prefix,
            'columns' => $this->getColumnNames($prefix.$tablename)
        ];
        
        $this->generateFilesFromTemplates($tablename, $options);

        $addroute = 'Route::controller(\'/'.$tablename_plural.'\', \''.ucfirst($tablename).'Controller\');';
        $this->appendToEndOfFile(app_path().'/Http/routes.php', "\n".$addroute, 0, true);
        
        
    }

    protected function getColumnNames($tablename) {
        $cols = DB::select("show columns from ".$tablename);
        $ret = [];
        foreach ($cols as $c) {
            $ret[] = $c->Field;
        }
        return $ret;
    }

    protected function generateFilesFromTemplates($tablename, $options) {

        $this->generateCatalogue('controller', app_path().'/Http/Controllers/'.ucfirst($tablename).'Controller.php', $options);
        $this->generateCatalogue('view.add', base_path().'/resources/views/'.str_plural($tablename).'/add.blade.php', $options);
        $htmlcolumns = "";
        foreach ($options['columns'] as $col) {
            $htmlcolumns .= "<th>".$col."</th>";
        }
        $options['htmlcolumns'] = $htmlcolumns;
        $options['num_columns'] = count($options['columns']);
        $this->generateCatalogue('view.index', base_path().'/resources/views/'.str_plural($tablename).'/index.blade.php', $options);
    }

    protected function createModel($tablename, $prefix = "", $singular = false, $custom_table = "") {
        Artisan::call('make:model', ['name' => ucfirst($tablename)]);

        if($singular || $custom_table) {
            $custom_table = $custom_table == null ? str_singular($tablename) : $this->argument('custom_table_name');
            $this->info('Custom table name: '.$prefix.$custom_table);
            $this->appendToEndOfFile(app_path().'/'.ucfirst($tablename).'.php', "    protected \$table = '".$custom_table."';\n}", 2);
        }
    }

    protected function deletePreviousFiles($tablename) {
        foreach([
                app_path().'/'.ucfirst($tablename).'.php',
                app_path().'/Http/Controllers/'.ucfirst($tablename).'Controller.php',
                base_path().'/resources/views/'.str_plural($tablename).'/index.blade.php',
                base_path().'/resources/views/'.str_plural($tablename).'/add.blade.php',
            ] as $path) {
            if(file_exists($path)) { 
                unlink($path);    
                $this->info('Deleted: '.$path);
            }   
        }
    }

    protected function renderWithData($template_path, $data) {
        $template = file_get_contents($template_path);
        foreach (array_keys($data) as $key) {
            if(!is_array($data[$key]))
                $template = str_replace('{{'.$key.'}}', $data[$key], $template);
        }
        return $template;
    }

    protected function generateCatalogue($template_name, $destination_path, $options) {
        $c = $this->renderWithData(__DIR__.'/../../Templates/'.$template_name.'.tpl.php', $options);
        file_put_contents($destination_path, $c);
        $this->info('Created Controller: '.$destination_path);

    }

    protected function appendToEndOfFile($path, $text, $remove_last_chars = 0, $dont_add_if_exist = false) {
        $content = file_get_contents($path);
        if(!str_contains($content, $text) || !$dont_add_if_exist) {
            $newcontent = substr($content, 0, strlen($content)-$remove_last_chars).$text;
            file_put_contents($path, $newcontent);    
        }
    }
}





















<?php

namespace CrudGenerator;


use Illuminate\Console\Command;
use DB;
use Artisan;

class CrudGeneratorService 
{
    
    public $modelName = '';
    public $tableName = '';
    public $prefix = '';
    public $recreate = false;
    public $layout = '';
    public $existingModel = '';
    public $controllerName = '';
    public $viewFolderName = '';

 
    public function __construct()
    {

    }

  
    public function Generate() 
    {
        $modelname = ucfirst(str_singular($this->modelName));
        $this->viewFolderName = strtolower($this->controllerName);

        $this->info('');
        $this->info('Creating catalogue for table: '.$this->tableName);
        $this->info('Model Name: '.$modelname);


        $options = [
            'model_uc' => $modelname,
            'model_uc_plural' => str_plural($modelname),
            'model_singular' => strtolower($modelname),
            'model_plural' => strtolower(str_plural($modelname)),
            'tablename' => $this->tableName,
            'prefix' => $this->prefix,
            'custom_master' => $this->layout ?: 'crudgenerator::layouts.master',
        ];

        //if($recreate) { $this->deletePreviousFiles($tablename, $existing_model); }
        /*if($existing_model) {
            $columns = $this->getColumns($prefix.str_plural($existing_model));
        }   
        else {
            $columns = $this->createModel($modelname, $prefix, $options['tablename']);
        }*/
        $columns = $this->createModel($modelname, $this->prefix, $this->tableName);
        
        $options['columns'] = $columns;
        $options['first_column_nonid'] = count($columns) > 1 ? $columns[1]['name'] : '';
        $options['num_columns'] = count($columns);
        
        //###############################################################################
        if(!is_dir(base_path().'/resources/views/'.$this->viewFolderName)) { 
            $this->info('Creating directory: '.base_path().'/resources/views/'.$this->viewFolderName);
            mkdir( base_path().'/resources/views/'.$this->viewFolderName); 
        }

        \CrudGenerator\CrudGeneratorFileCreator $filegenerator = new \CrudGenerator\CrudGeneratorFileCreator();
        $filegenerator->options = $options;

        $filegenerator->templateName = 'controller';
        $filegenerator->path = app_path().'/Http/Controllers/'.$this->controllerName.'Controller.php';
        $filegenerator->Generate();

        $filegenerator->templateName = 'view.add';
        $filegenerator->path = base_path().'/resources/views/'.$this->viewFolderName.'/add.blade.php';
        $filegenerator->Generate();

        $filegenerator->templateName = 'view.show';
        $filegenerator->path = base_path().'/resources/views/'.$this->viewFolderName.'/show.blade.php';
        $filegenerator->Generate();

        $filegenerator->templateName = 'view.index';
        $filegenerator->path = base_path().'/resources/views/'.$this->viewFolderName.'/index.blade.php';
        $filegenerator->Generate();
        //###############################################################################

        $addroute = 'Route::controller(\'/'.$this->viewFolderName.'\', \''.$this->controllerName.'Controller\');';
        $this->appendToEndOfFile(app_path().'/Http/routes.php', "\n".$addroute, 0, true);
    }


    protected function getColumns($tablename) {
        $cols = DB::select("show columns from ".$tablename);
        $ret = [];
        foreach ($cols as $c) {
            $cadd = [];
            $cadd['name'] = $c->Field;
            $cadd['type'] = $c->Field == 'id' ? 'id' : $this->getTypeFromDBType($c->Type);
            $cadd['display'] = ucwords(str_replace('_', ' ', $c->Field));
            $ret[] = $cadd;
        }
        return $ret;
    }

    protected function getTypeFromDBType($dbtype) {
        if(str_contains($dbtype, 'varchar')) { return 'text'; }
        if(str_contains($dbtype, 'int') || str_contains($dbtype, 'float')) { return 'number'; }
        if(str_contains($dbtype, 'date')) { return 'date'; }
        return 'unknown';
    }



    protected function createModel($modelname, $prefix,$table_name) {

        Artisan::call('make:model', ['name' => $modelname]);
        

        if(str_plural(strtolower($modelname)) != $table_name) {
            $this->info('Custom table name: '.$prefix.$custom_table);
            $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "    protected \$table = '".$table_name."';\n\n}", 2);
        }
        else {
            $custom_table = $table_name;
        }

        $columns = $this->getColumns($prefix.$custom_table);

        $cc = collect($columns);

        if(!$cc->contains('name', 'updated_at') || !$cc->contains('name', 'created_at')) { 
            $this->appendToEndOfFile(app_path().'/'.$modelname.'.php', "    public \$timestamps = false;\n\n}", 2);
        }

        $this->info('Model created, columns: '.json_encode($columns));
        return $columns;
    }

    protected function deletePreviousFiles($tablename, $existing_model) {
        $todelete = [
                app_path().'/Http/Controllers/'.ucfirst($tablename).'Controller.php',
                base_path().'/resources/views/'.str_plural($tablename).'/index.blade.php',
                base_path().'/resources/views/'.str_plural($tablename).'/add.blade.php',
                base_path().'/resources/views/'.str_plural($tablename).'/show.blade.php',
            ];
        if(!$existing_model) {
            $todelete[] = app_path().'/'.ucfirst(str_singular($tablename)).'.php'; 
        }
        foreach($todelete as $path) {
            if(file_exists($path)) { 
                unlink($path);    
                $this->info('Deleted: '.$path);
            }   
        }
    }

    protected function appendToEndOfFile($path, $text, $remove_last_chars = 0, $dont_add_if_exist = false) {
        $content = file_get_contents($path);
        if(!str_contains($content, $text) || !$dont_add_if_exist) {
            $newcontent = substr($content, 0, strlen($content)-$remove_last_chars).$text;
            file_put_contents($path, $newcontent);    
        }
    }
}





















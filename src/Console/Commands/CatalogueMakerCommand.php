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
        $tablename = $this->argument('name');
        $modelname = ucfirst($tablename);
        $prefix = \Config::get('database.connections.mysql.prefix');

        $this->info('Creating catalogue for table: '.$tablename);
        $this->info('Model Name: '.$modelname);
        
        $modelpath = app_path().'/'.$modelname.'.php';
        if($this->option('recreate')) {
            foreach([
                    $modelpath,
                    app_path().'/Http/Controllers/'.$modelname.'Controller.php'
                ] as $path) {
                if(file_exists($path)) unlink($path);    
                $this->info('Deleted: '.$path);
            }
        }

        $exitCode = Artisan::call('make:model', ['name' => $modelname]);

        if($this->argument('custom_table_name') || $this->option('singular')) {
            $custom_table = $this->argument('custom_table_name');
            if($custom_table == null) { $custom_table = str_singular($tablename); }  
            $this->info('Custom table name: '.$prefix.$custom_table);

            
            $content = file_get_contents($modelpath);
            $newcontent = substr($content, 0, strlen($content)-2)."    protected \$table = '".$custom_table."';\n}";
            file_put_contents($modelpath, $newcontent);

        }
        //$propers = get_class_vars('Tipocosto');

        $modelfull = '\App\\'.$modelname;
        $this->info('Example data: '.$modelfull::first());

        //$c = Iluminate\View\Compilers\BladeCompiler::compileString()
        
        $options = [
                'model_uc' => $modelname,
                'model_singular' => $tablename,
                'model_plural' => str_plural($tablename)
            ];
        $this->generateCatalogue('controller', app_path().'/Http/Controllers/'.$modelname.'Controller.php', $options);
        $this->generateCatalogue('controller', app_path().'/Http/Controllers/'.$modelname.'Controller.php', $options);
        
        //$headers = ['Name', 'Email'];
        //$users = App\User::all(['name', 'email'])->toArray();

        //$this->table($headers, $users);
        //$this->info('Properties: '.print_r($propers, true));
    }

    protected function renderWithData($template_path, $data) {
        $template = file_get_contents($template_path);
        foreach (array_keys($data) as $key) {
            $template = str_replace('{{'.$key.'}}', $data[$key], $template);
        }
        return $template;
    }

    protected function generateCatalogue($template_name, $destination_path, $options) {
        $c = $this->renderWithData(__DIR__.'/../../Templates/'.$template_name.'.tpl.php', $options);
        file_put_contents($destination_path, $c);
        $this->info('Created Controller: '.$destination_path);

    }
}





















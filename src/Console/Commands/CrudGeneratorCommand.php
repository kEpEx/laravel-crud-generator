<?php

namespace CrudGenerator\Console\Commands;

use Illuminate\Console\Command;
use DB;
use Artisan;

class CrudGeneratorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:crud {model-name} {--recreate} {--singular} {--table-name=} {--master-layout=} {--custom-controller=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create fully functional CRUD code based on a mysql table instantly';

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
        $modelname = strtolower($this->argument('model-name'));
        $prefix = \Config::get('database.connections.mysql.prefix');
        $custom_table_name = $this->option('table-name');
        $custom_controller = $this->option('custom-controller');
        $singular = $this->option('singular');

        $tocreate = [];

        if($tablename == 'all') {
            $pretables = json_decode(json_encode(DB::select("show tables")), true);
            $tables = [];
            foreach($pretables as $p) { 
                list($key) = array_keys($p);
                $tables[] = $p[$key]; 
            }
            $this->info("List of tables: ".print_r($tables, true));
            
            foreach ($tables as $t) {
                // Ignore tables with different prefix
                if($prefix == '' || str_contains($t, $prefix))
                    $tablenames[] = strtolower(substr($t, strlen($prefix)));
            }
            // Custom table name and existing model, should not have effect for whole database
            $custom_table_name = null;
            $custom_controller = null;
        }
        else {
            $tocreate = [
                'modelname'=> $modelname,
            ];
            if($modelname == $singular) {
                $tocreate['tablename'] = $modelname;    
            }
            else if($custom_table_name) { 
                $tocreate['tablename'] = $custom_table_name; 
            }
            else { 
                $tocreate['tablename'] = ''; 
            }

            $tocreate = [$tocreate];
        }

        foreach ($tocreate as $c) {
            \CrudGenerator\CrudGeneratorService $generator = new \CrudGenerator\CrudGeneratorService();

            $generator->modelName = ucfirst($c['modelname']);
            $generator->tableName = $c['tablename'];

            $generator->prefix = $prefix;
            $generator->recreate = $this->option('recreate');
            $generator->layout = $this->option('master-layout');
            $generator->controllerName = ucfirst(strtolower($custom_controller)) ?: str_plural($generator->modelName);

            $generator->Generate();
        }

    }

    
}





















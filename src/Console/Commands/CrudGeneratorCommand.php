<?php

namespace CrudGenerator\Console\Commands;

//namespace App\Console\Commands;

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
    protected $signature = 'make:crud {name} {--recreate} {--table-name=} {--master-layout=}';

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
        $tablename = strtolower($this->argument('name'));
        $prefix = \Config::get('database.connections.mysql.prefix');
        $custom_table_name = $this->option('table-name');
        $tablenames = [];

        if($tablename == 'all') {
            $pretables = json_decode(json_encode(DB::select("show tables")), true);
            $tables = [];
            foreach($pretables as $p) { 
                list($key) = array_keys($p);
                $tables[] = $p[$key]; 
            }
            $this->info("List of tables: ".print_r($tables, true));
            
            foreach ($tables as $t) {
                if($prefix == '' || str_contains($t, $prefix))
                    $tablenames[] = strtolower(substr($t, strlen($prefix)));
                //else 
                    //$tablenames[] = strtolower($t);
            }
            $custom_table_name = null;
        }
        else {
            $tablenames = [$tablename];
        }

        foreach ($tablenames as $table) {
            //$this->info($table);
            $singular = false;
            if($table == str_singular($table)) { $singular = true; }
            $this->createCRUDFor($table, $prefix, $singular, $custom_table_name, $this->option('recreate'), $this->option('master-layout'));    
        }

    }

    protected function createCRUDFor($tablename, $prefix, $singular, $custom_table_name, $recreate, $custom_master) {
        $this->info('');
        $this->info('Creating catalogue for table: '.$tablename);
        $this->info('Model Name: '.ucfirst($tablename));

        if($recreate) { $this->deletePreviousFiles($tablename); }
        $columns = $this->createModel($tablename, $prefix, $singular, $custom_table_name);
        $options = [
            'model_uc' => ucfirst($tablename),
            'model_singular' => $tablename,
            'model_plural' => str_plural($tablename),
            'tablename' => $custom_table_name ? $custom_table_name : ($singular ? str_singular($tablename) : $tablename),
            'prefix' => $prefix,
            'columns' => $columns,
            'first_column_nonid' => count($columns) > 1 ? $columns[1]['name'] : '',
            'num_columns' => count($columns),
            'custom_master' => $custom_master ?: 'crudgenerator::layouts.master',
        ];
        
        $this->generateFilesFromTemplates($tablename, $options);

        $addroute = 'Route::controller(\'/'.str_plural($tablename).'\', \''.ucfirst($tablename).'Controller\');';
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



    protected function generateFilesFromTemplates($tablename, $options) {
        if(!is_dir(base_path().'/resources/views/'.str_plural($tablename))) { 
            $this->info('Creating directory: '.base_path().'/resources/views/'.str_plural($tablename));
            mkdir( base_path().'/resources/views/'.str_plural($tablename)); 
        }
        $this->generateCatalogue('controller', app_path().'/Http/Controllers/'.ucfirst($tablename).'Controller.php', $options);
        $this->generateCatalogue('view.add', base_path().'/resources/views/'.str_plural($tablename).'/add.blade.php', $options);
        $this->generateCatalogue('view.show', base_path().'/resources/views/'.str_plural($tablename).'/show.blade.php', $options);
        $this->generateCatalogue('view.index', base_path().'/resources/views/'.str_plural($tablename).'/index.blade.php', $options);
    }

    protected function createModel($tablename, $prefix = "", $singular = false, $custom_table = "") {

        Artisan::call('make:model', ['name' => ucfirst($tablename)]);
        

        if($singular || $custom_table) {
            $custom_table = $custom_table == null ? str_singular($tablename) : $custom_table;    
            $this->info('Custom table name: '.$prefix.$custom_table);
            $this->appendToEndOfFile(app_path().'/'.ucfirst($tablename).'.php', "    protected \$table = '".$custom_table."';\n\n}", 2);
        }
        else {
            $custom_table = $tablename;
        }

        $columns = $this->getColumns($prefix.$custom_table);

        $cc = collect($columns);

        if(!$cc->contains('name', 'updated_at') || !$cc->contains('name', 'created_at')) { 
            $this->appendToEndOfFile(app_path().'/'.ucfirst($tablename).'.php', "    public \$timestamps = false;\n\n}", 2);
        }

        $this->info('Model created, columns: '.json_encode($columns));
        return $columns;
    }

    protected function deletePreviousFiles($tablename) {
        foreach([
                app_path().'/'.ucfirst($tablename).'.php',
                app_path().'/Http/Controllers/'.ucfirst($tablename).'Controller.php',
                base_path().'/resources/views/'.str_plural($tablename).'/index.blade.php',
                base_path().'/resources/views/'.str_plural($tablename).'/add.blade.php',
                base_path().'/resources/views/'.str_plural($tablename).'/show.blade.php',
            ] as $path) {
            if(file_exists($path)) { 
                unlink($path);    
                $this->info('Deleted: '.$path);
            }   
        }
    }

    protected function renderWithData($template_path, $data) {
        $template = file_get_contents($template_path);
        $template = $this->renderForeachs($template, $data);
        $template = $this->renderIFs($template, $data);
        $template = $this->renderVariables($template, $data);
        return $template;
    }

    protected function renderVariables($template, $data) {
        $callback = function ($matches) use($data) {
            if(array_key_exists($matches[1], $data)) {
                return $data[$matches[1]];
            }
            return $matches[0];
        };
        $template = preg_replace_callback('/\[\[\s*(.+?)\s*\]\](\r?\n)?/s', $callback, $template);
        return $template;
    }

    protected function renderForeachs($template, $data) {
        $callback = function ($matches) use($data) {
            $rep = $matches[0];
            $rep = preg_replace('/\[\[\s*foreach:\s*(.+?)\s*\]\](\r?\n)?/s', '', $rep);
            $rep = preg_replace('/\[\[\s*endforeach\s*\]\](\r?\n)?/s', '', $rep);
            $ret = '';
            if(array_key_exists($matches[1], $data) && is_array($data[$matches[1]])) {

                $parent = $data[$matches[1]];
                foreach ($parent as $i) {
                    $d = [];
                    if(is_array($i)) {
                        foreach ($i as $key => $value) {
                            $d['i.'.$key] = $value;
                        }
                    }
                    else {
                        $d['i'] = $i;
                    }
                    $rep2 = $this->renderIFs($rep, array_merge($d, $data));
                    $rep2 = $this->renderVariables($rep2, array_merge($d, $data));
                    $ret .= $rep2;
                }
                return $ret;
            }
            else {
                return $mat;    
            }
            
        };
        $template = preg_replace_callback('/\[\[\s*foreach:\s*(.+?)\s*\]\](\r?\n)?((?!endforeach).)*\[\[\s*endforeach\s*\]\](\r?\n)?/s', $callback, $template);
        return $template;
    }

    protected function getValFromExpression($exp, $data) {
        if(str_contains($exp, "'")) {
            return trim($exp,"'");    
        }
        else {
            if(array_key_exists($exp, $data)) {
                return $data[$exp];
            }
            else return null;
        }
    }

    protected function renderIFs($template, $data) {
        $callback = function ($matches) use($data) {
            $rep = $matches[0];
            $rep = preg_replace('/\[\[\s*if:\s*(.+?)\s*([!=]=)\s*(.+?)\s*\]\](\r?\n)?/s', '', $rep);
            $rep = preg_replace('/\[\[\s*endif\s*\]\](\r?\n)?/s', '', $rep);
            $ret = '';
            $val1 = $this->getValFromExpression($matches[1], $data);
            $val2 = $this->getValFromExpression($matches[3], $data);
            if($matches[2] == '==' && $val1 == $val2) { $ret .= $rep; }
            if($matches[2] == '!=' && $val1 != $val2) { $ret .= $rep; }
            
            return $ret;
        };
        $template = preg_replace_callback('/\[\[\s*if:\s*(.+?)\s*([!=]=)\s*(.+?)\s*\]\](\r?\n)?((?!endif).)*\[\[\s*endif\s*\]\](\r?\n)?/s', $callback, $template);
        return $template;
    }

    protected function generateCatalogue($template_name, $destination_path, $options) {
        $c = $this->renderWithData($this->customTemplateOfDefault($template_name), $options);
        file_put_contents($destination_path, $c);
        $this->info('Created Controller: '.$destination_path);

    }

    protected function customTemplateOfDefault($template_name) {
        $trypath = base_path().'/resources/templates/'.$template_name.'.tpl.php';
        if(file_exists($trypath)) return $trypath;
        return __DIR__.'/../../Templates/'.$template_name.'.tpl.php';
    }

    protected function appendToEndOfFile($path, $text, $remove_last_chars = 0, $dont_add_if_exist = false) {
        $content = file_get_contents($path);
        if(!str_contains($content, $text) || !$dont_add_if_exist) {
            $newcontent = substr($content, 0, strlen($content)-$remove_last_chars).$text;
            file_put_contents($path, $newcontent);    
        }
    }
}





















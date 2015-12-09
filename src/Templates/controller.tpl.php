<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\[[model_uc]];

use DB;

class [[model_uc]]Controller extends Controller
{
    //
    public function __construct()
    {
        //$this->middleware('auth');
    }


    public function getIndex(Request $request)
	{
		//$[[model_plural]] = DB::select("select *,u.name as user_name from [[model_plural]] t join users u on t.user_id=u.id");
		$[[model_plural]] = [[model_uc]]::orderBy('id', 'desc')->get();

	    return view('[[model_plural]].index', [
	        '[[model_plural]]' => $[[model_plural]]
	    ]);
	}

	public function getAdd(Request $request)
	{
	    return view('[[model_plural]].add', [
	        []
	    ]);
	}

	public function getUpdate(Request $request, $id)
	{
		$[[model_singular]] = [[model_uc]]::findOrFail($id);
	    return view('[[model_plural]].add', [
	        'model' => $[[model_singular]]
	    ]);
	}

	public function getShow(Request $request, $id)
	{
		$[[model_singular]] = [[model_uc]]::findOrFail($id);
	    return view('[[model_plural]].show', [
	        'model' => $[[model_singular]]
	    ]);
	}

	public function getGrid(Request $request)
	{
		$len = $_GET['length'];
		$start = $_GET['start'];

		$select = "SELECT *,1,2 ";
		$presql = " FROM [[prefix]][[tablename]] a ";
		if($_GET['search']['value']) {	
			$presql .= " WHERE [[first_column_nonid]] LIKE '%".$_GET['search']['value']."%' ";
		}
		
		$presql .= "  ";

		$sql = $select.$presql." LIMIT ".$start.",".$len;


		$qcount = DB::select("SELECT COUNT(a.id) c".$presql);
		//print_r($qcount);
		$count = $qcount[0]->c;

		$results = DB::select($sql);
		$ret = [];
		foreach ($results as $row) {
			$r = [];
			foreach ($row as $value) {
				$r[] = $value;
			}
			$ret[] = $r;
		}

		$ret['data'] = $ret;
		$ret['recordsTotal'] = $count;
		$ret['iTotalDisplayRecords'] = $count;

		$ret['recordsFiltered'] = count($ret);
		$ret['draw'] = $_GET['draw'];

		echo json_encode($ret);

	}


	public function postSave(Request $request) {
	    //
	    /*$this->validate($request, [
	        'name' => 'required|max:255',
	    ]);*/
		$[[model_singular]] = null;
		if($request->id > 0) { $[[model_singular]] = [[model_uc]]::findOrFail($request->id); }
		else { 
			$[[model_singular]] = new [[model_uc]];
		}
	    

	    [[foreach:columns]]
	    $[[model_singular]]->[[i.name]] = $request->[[i.name]];
	    [[endforeach]]
	    //$[[model_singular]]->user_id = $request->user()->id;
	    $[[model_singular]]->save();

	    return redirect('/[[model_plural]]/index');

	}

	public function getDelete(Request $request, $id) {
		
		$[[model_singular]] = [[model_uc]]::findOrFail($id);

		$[[model_singular]]->delete();
		return redirect('/[[model_plural]]/index');
	    
	}

	
}

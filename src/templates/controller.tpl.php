<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\{{model_uc}};

use DB;

class {{model_uc}}Controller extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function getIndex(Request $request)
	{
		//${{model_plural}} = DB::select("select *,u.name as user_name from {{model_plural}} t join users u on t.user_id=u.id");
		${{model_plural}} = {{model_uc}}::orderBy('id', 'desc')->get();

	    return view('{{model_plural}}', [
	        '{{model_plural}}' => ${{model_plural}}
	    ]);
	}


	public function postIndex(Request $request) {
	    //
	    $this->validate($request, [
	        'name' => 'required|max:255',
	    ]);

	    ${{model_singular}} = new {{model_uc}};
	    ${{model_singular}}->name = $request->name;
	    //${{model_singular}}->user_id = $request->user()->id;
	    ${{model_singular}}->save();

	    return redirect('/{{model_plural}}');

	}

	public function deleteDestroy(Request $request, $id) {
		
		${{model_singular}} = {{model_uc}}::findOrFail($id);

		if($request->user()->id == ${{model_singular}}->user_id) {
			 ${{model_singular}}->delete();
			 return redirect('/{{model_plural}}');
		}
		else {
			//print_r(${{model_singular}});
			//return view('{{model_plural}}');
			return redirect('/{{model_plural}}?error');	
		}
	    
	}


}

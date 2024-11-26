<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserController extends Controller
{

	public function __construct()
    {
        $this->middleware('auth');
    }

    public function get_users(Request $request){
    	$stateId=$request->input('stateId');
    	$cityId=$request->input('cityId');

    	$users=DB::table('users');

    	if($stateId && $stateId!=='all'){
    		$users=$users->whereRaw('FIND_IN_SET(?,state_id)',[$stateId]);
    	}

    	if($cityId && $cityId!=='all'){
    		$users=$users->whereRaw('FIND_IN_SET(?,city_id)',[$cityId]);
    	}

    	$users=$users->get()->toArray();

    	if(!empty($users)){
    		foreach($users as $user){ ?>
    			<option value="<?=$user->id;?>" <?= count($users)==1?'selected="selected"':''; ?> ><?=$user->name.' '.$user->lastname; ?></option>
    		<?php }
    	}
    }

	//accountant list
	public function index(){
	    $accountant=DB::table('users')->where('role','=','accountant')->orderBy('id','DESC')->get()->toArray();
		return view('accountant.list',compact('accountant'));
	}


	//accountant show
	public function usershow($id)
	{
		$viewid = $id;
	    $user=DB::table('users')->where('id','=',$id)->first();
		return view('user.view',compact('user','title','viewid'));
	}

	//accountant delete
	public function destory($id)
	 {
		$user = \App\Models\User::findOrFail($id);
        $this->authorize('edit', $user);
        $user->delete();

		return redirect('/accountant/list')->with('message','Successfully Deleted');
	 }

    //accountant edit
	public function accountantedit($id)
	{
        $accountant = User::findOrFail($id);
        $this->authorize('edit', $accountant);

        $editid=$id;
		$country = DB::table('tbl_countries')->get()->toArray();
		$state = DB::table('tbl_states')->get()->toArray();
		$city = DB::table('tbl_cities')->get()->toArray();

		return view('accountant.update',compact('country','accountant','state','city','editid'));
	}
}

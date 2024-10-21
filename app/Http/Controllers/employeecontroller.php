<?php

namespace App\Http\Controllers;

use App\Models\DefaultModels\User;
use App\Models\PreparedCompanies;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use App\Models\DefaultModels\tbl_activities;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailer;
use PhpOffice\PhpSpreadsheet\IOFactory;

class employeecontroller extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function employeelist()
    {
        $user = Auth::User();

        $users = User::
        select(
            'users.*',
            'tbl_accessrights.name as position'
        )->
        join('tbl_accessrights', 'tbl_accessrights.id', '=', 'users.role');

        $users = $users->where('users.id', '!=', $user->id)->orderBy('id', 'DESC')->get();
        if(auth()->user()->role=="admin"){
            $users=User::orderBy('id', 'DESC')->get();
        }

        return view('employee.list', compact('users'));
    }


    // employee addform

    public function addemployee()
    {
        $states = DB::table('tbl_states')->get()->toArray();
        $country = DB::table('tbl_countries')->get()->toArray();
        $roles = DB::table('tbl_accessrights')->where('status', '=', 'active')->get()->toArray();

        return view('employee.add', compact('country', 'roles', 'states'));

    }


    // employee store

    public function store(Request $request)
    {
        $this->authorize('create', new User);
        $validated = $request->validate([
            'email' => 'required|unique:users|max:255',
            'password' => 'required',
        ]);
        $firstname = $request->input('firstname');
        $email = $request->input('email');

        $password = $request->input('password');

        if (getDateFormat() == 'm-d-Y') {
            $dob = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('dob'))));
        } else {
            $dob = date('Y-m-d', strtotime($request->input('dob')));
        }

        $user = new User;
        $user->name = $firstname;
        $user->lastname = $request->input('lastname');
        $user->display_name = $request->input('displayname');
        $user->gender = $request->input('gender');
        $user->birth_date = join('-', array_reverse(explode('-', $request->input('dob'))));
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->mobile_no = $request->input('mobile');
        $user->address = $request->input('address');
        $user->api_token = auth()->user()->createToken('authToken')->accessToken;
        if (!empty($request->hasFile('image'))) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path() . '/employee/', $file->getClientOriginalName());
            $user->image = $filename;
        } else {
            $user->image = 'avtar.png';
        }
        $user->role = $request->input('role');
        $user->save();
        $last_id = DB::table('users')->orderBy('id', 'desc')->get()->first();
        $userA = Auth::user();
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $last_id->id;
        $active->action_type = 'user_added';
        $active->action = "Foydalanuvchi qo'shildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        return redirect('/employee/list')->with('message', 'Successfully Submitted');

    }


    public function getrole(Request $request)
    {
        $position = $request->input('position');
        $role = DB::table('tbl_accessrights')->where('id', '=', $position)->get()->first();
        echo $role->position;
    }


    // employee edit

    public function edit($id)
    {
        $editid = $id;
        $title = "Xodimni o'zgartirish";
        $user = User::find($editid);

        $this->authorize('edit', User::class);
        if ($user->role != 'admin') {
            $position = DB::table('tbl_accessrights')->where('id', '=', intval($user->role))->get()->first();
            if (!empty($position)) {
                if ($position->position == 'district') {
                    $state = DB::table('tbl_states')->get()->toArray();
                    $cities = DB::table('tbl_cities')->get()->toArray();
                } elseif ($position->position == 'country') {
                    $state = DB::table('tbl_states')->where(function ($query) use ($user) {
                        foreach (explode(',', $user->state_id) as $city) {
                            $query->orWhere('tbl_states.id', '=', $city);
                        }
                    })->get()->toArray();
                    $cities = DB::table('tbl_cities')->where('id', '=', $user->city_id)->get()->toArray();
                } elseif ($position->position == 'region') {
                    $state = DB::table('tbl_states')->where('id', '=', $user->state_id)->get()->toArray();
                    $cities = DB::table('tbl_cities')->where('id', $user->city_id)->get()->toArray();
                }
            }
        }
        $country = DB::table('tbl_countries')->get()->toArray();
        $state=null;
        $cities=null;

        $position = DB::table('tbl_accessrights')->where('id', '=', intval($user->role))->get()->first();
        $roles = DB::table('tbl_accessrights')->where('status', '=', 'active')->get()->toArray();
        return view('employee.edit', compact('country', 'state', 'cities', 'user', 'editid', 'roles', 'position', 'title'));
    }


    // employee update

    public function update($id, Request $request)
    {
        $this->authorize('edit', User::class);

        $firstname = $request->input('firstname');
        $email = $request->input('email');
        $password = $request->input('password');

        if (getDateFormat() == 'm-d-Y') {
            $dob = date('Y-m-d', strtotime(str_replace('-', '/', $request->input('dob'))));
        } else {
            $dob = date('Y-m-d', strtotime($request->input('dob')));
        }
        $userold = DB::table('users')->where('id', '=', $id)->get()->first();
        if ($userold->role == 'admin') {
            $role = 'admin';
        } else {
            $role = $request->input('role');
        }
        $user = User::find($id);
        $user->name = $firstname;
        $user->lastname = $request->input('lastname');
        $user->display_name = $request->input('displayname');
        $user->gender = $request->input('gender');
        $user->birth_date = join('-', array_reverse(explode('-', $request->input('dob'))));
        $user->email = $email;
        $user->api_token=$user->api_token??auth()->user()->createToken('authToken')->accessToken;
        if (!empty($password)) {
            $user->password = bcrypt($password);
        }
        $user->mobile_no = $request->input('mobile');
        $user->address = $request->input('address');
        if (!empty($request->hasFile('image'))) {
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $file->move(public_path() . '/employee/', $file->getClientOriginalName());
            $user->image = $filename;
        }
        $user->role = $role;
        $user->save();

        $userA = Auth::user();
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = $userA->id;
        $active->action_id = $id;
        $active->action_type = 'user_edit';
        $active->action = "Foydalanuvchi O'zgartrildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        return redirect('/employee/list')->with('message', 'Successfully Updated');

    }

    public function showemployer($id)
    {
        $user = User::findOrFail($id);

        $this->authorize('view', User::class);

        return view('employee.show', compact('user'));
    }

    public function destory($id)
    {
        $user = User::findOrFail($id);
        $this->authorize('edit', User::class);

        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->user_id = auth()->id();
        $active->action_id = $id;
        $active->action_type = 'user_deleted';
        $active->action = "Inspektor O'chirildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        $user->delete();

        return redirect('employee/list')->with('message', 'Successfully Deleted');
    }

    public function add_users()
    {
        $states = DB::table('tbl_states')->get()->toArray();

        return view('employee.add_users', compact( 'states'));

    }


    // employee store

    public function add_store(Request $request)
    {

        if ($request->hasFile('file')) {
            $file = $request->file('file');
        }
//        try {
            // Load the Excel file
            $spreadsheet = IOFactory::load($file);
            $worksheet = $spreadsheet->getActiveSheet();

            // Iterate through rows and columns to read data
            $excelData = [];
            foreach ($worksheet->getRowIterator() as $row) {
                $rowData = [];
                foreach ($row->getCellIterator() as $cell) {
                    $rowData[] = $cell->getValue();
                }
                $excelData[] = $rowData;
            }

            $userData = [];

            // Fetch both 'id' and 'kod' columns in a single query
            $factories = PreparedCompanies::orderBy('id')->get(['id', 'kod']);

            foreach ($excelData as $data){

                $zavodId = null;

                // Use firstWhere to find the matching 'kod' value
                $matchingFactory = $factories->firstWhere('kod', $data[1]);

                // If a match is found, assign the 'id' value to $zavodId
                if ($matchingFactory) {
                    $zavodId = $matchingFactory->id;
                }
                //split full name to array
                $nameParts = explode(' ', $data[0]);
                $firstPart = isset($nameParts[2] ) ? $nameParts[2] : ' ' ;
                $secondPart = isset($nameParts[3]) ? $nameParts[3] : '' ;

                //collecting user data
                $userData[] = [
                    'name' => $nameParts[0],
                    'lastname' => $nameParts[1],
                    'display_name' => $firstPart. ' '. $secondPart,
                    'role' => \App\Models\User::ROLE_CITY_EMPLOYEE,
                    'state_id' => $data[2],
                    'branch_id' => 2,
                    'crop_branch' => 2,
                    'zavod_id' => $zavodId,
                    'gender' => 6,
                    'birth_date' => '1970-01-01',
                    'email' => 'laboratoriya'. $data[1]. '@gmail.com',
                    'password' => Hash::make($data[6]),
                    'mobile_no' => $data[4],
                    'status' => 1
                ];
            }
            if($userData){
                \App\Models\User::insert($userData);
            }
            return redirect('/employee/list')->with('message', 'Successfully Submitted');
//        } catch (\Exception $e) {
//            // Handle any exceptions that may occur during file reading
//            return "Error: " . $e->getMessage();
//        }
    }

}


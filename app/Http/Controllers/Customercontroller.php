<?php

namespace App\Http\Controllers;

use App\CustomerCategories;
use App\DriverExam;
use App\DriverExamType;
use App\Http\Controllers\excelImporter\SpreadsheetReader;
use App\Http\Controllers\excelImporter\Transliteration;
use App\Services\LocationService;
use App\Http\Requests;
use App\Http\Requests\Admin\Customer\StoreRequest;
use App\Http\Resources\Api\CustomerSearchResultResource;
use App\Models\Area;
use App\Models\Customer;
use App\Models\CustomerCategory;
use App\Models\DriverLicence;
use App\Models\OwnershipForm;
use App\Models\Region;
use App\Models\VehicleCertificate;
use App\Models\VehicleInspection;
use App\Models\VehicleRegistration;
use App\Services\PdfGeneratorCertificate;
use App\Services\PdfGeneratorTexPass;
use App\tbl_activities;
use App\tbl_settings;
use App\tbl_vehicles;
use App\TechnicalPassport;
use App\TransportNumber;
use App\vehicle_certificates;
use App\vehicle_prohibitions;
use App\vehicle_registrations;
use Illuminate\Support\Facades\Auth;
use DateTime;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Mail;
use URL;
use App\ViewModels\AgrotehApiModel;
use Illuminate\Pagination\LengthAwarePaginator;

class Customercontroller extends Controller{

    public function __construct(){
        $this->middleware('auth');
    }

    //customer addform

    public function customeradd(){
        $title="Texnika egasi qo'shish";
        $currentUser=Auth::user();
        $currentRole=DB::table('tbl_accessrights')->where('id','=',$currentUser->role)->first();

        if($currentUser->role=='admin' || $currentRole->position == 'country' || $currentRole->position=='region'){
            $states = Region::when(auth()->user()->region_ids, function ($query) {
                $query->whereIn('id', auth()->user()->region_ids);
            })->orderBy('name')->get();
            if ($states->count() === 1) {
                $cities= Area::when(auth()->user()->region_ids, function ($query, $regionIds) {
                    $query->whereIn('state_id', $regionIds);
                })->orderBy('name')->get();
            } else {
                $cities = '';
            }
        } else {
            $states = Region::when(auth()->user()->region_ids, function ($query) {
                $query->whereIn('id', auth()->user()->region_ids);
            })->orderBy('name')->get();
            $cities= Area::when(auth()->user()->area_ids && auth()->user()->state_id != TASHKENT_ID, function ($query) {
                $query->whereIn('id', auth()->user()->area_ids);
            })
                ->when(auth()->user()->state_id == TASHKENT_ID, function ($query) {
                    $query->whereIn('id', Area::where('state_id', TASHKENT_ID)->pluck('id'));
                })
                ->when(auth()->user()->region_ids, function ($query, $regionIds) {
                    $query->whereIn('state_id', $regionIds);
                })->orderBy('name')->get();
        }

        return view('customer.add', [
                'model' => new Customer,
                'categories' => CustomerCategory::all(),
                'ownershipForms' => OwnershipForm::all(),
            ] + compact('states', 'cities', 'title'));
    }

    // customer edit
    public function customeredit($id)
    {
        $customer = Customer::findOrFail($id);
        $this->authorize('edit', $customer);

        $editid=$id;
        $currentUser=Auth::user();
        $currentRole=DB::table('tbl_accessrights')->where('id','=',$currentUser->role)->first();

        if($currentUser->role=='admin' || $currentRole->position == 'country'){
            $states = DB::table('tbl_states')->where('country_id', '=', 234)->orderBy('name')->get()->toArray();
            $cities='';
        }elseif($currentRole->position=='region'){
            $states = DB::table('tbl_states')->where('id', '=', $currentUser->state_id)->orderBy('name')->get()->toArray();
            $cities=DB::table('tbl_cities')->where('state_id','=',$currentUser->state_id)->get()->toArray();
        }elseif($currentRole->position=='district'){
            $states = DB::table('tbl_states')->where('id', '=', $currentUser->state_id)->orderBy('name')->get()->toArray();
            $cities=DB::table('tbl_cities')->whereIn('id',explode(',',$currentUser->city_id))->whereIn('id',explode(',',$currentUser->city_id))->orderBy('name')->get()->toArray();
        }

        if(!empty($customer) && $customer->type=='physical' && $customer->p_given_city && $customer->residence==1){

            $p_given_city=DB::table('tbl_cities')->where('id','=',$customer->p_given_city)->first();

        }

        if(!empty($customer) &&  $customer->city_id){

            $customer_city=DB::table('tbl_cities')->where('id','=',$customer->city_id)->first();

        }

        $title=$customer->name.' '.$customer->lastname.' - Tahrirlash';

        return view('customer.add', [
                'model' => $customer,
                'categories' => CustomerCategory::all(),
                'ownershipForms' => OwnershipForm::all(),
            ] + compact('states','cities', 'editid','customer','p_given_city','customer_city','title'));
    }



    //customer store
    public function storecustomer(StoreRequest $request)
    {
        $customer = new Customer($request->validated());

        $customer->type = $request->input('customer_type');

        $customer->name = $request->input('name');
        $customer->lastname = $request->input('lastname');
        $customer->middlename = $request->input('middlename');

        $customer->filial_of = $request->input('filial_of');
        $customer->form = $request->input('ownership_form');

        $customer->category = $request->input('category');

        $customer->d_o_birth = join('-', array_reverse(explode('-', $request->input('dob'))));

        $customer->id_number = $request->input('id_number');

        $customer->passport_series = $request->input('passport_series');

        $customer->passport_number = $request->input('passport_number');

        $customer->p_given_date = join('-', array_reverse(explode('-', $request->input('p_given_date'))));

        $customer->p_given_city = $request->input('p_given_city');

        $customer->mobile = cleanPhone($request->input('mobile'));

        $customer->email = $request->input('email');

        $customer->city_id = $request->input('city');

        $customer->address = $request->input('address');
        $customer->user_id = Auth::user()->id;
        $customer->id_number = $request->input('id_number');
        $customer->residence = $request->input('residence');
        $customer->save();

        $last_id = DB::table('customers')->orderBy('id', 'desc')->get()->first();
        $owner = DB::table('customers')->orderBy('id', 'desc')->get()->first();

        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $owner->id;
        $active->user_id = Auth::user()->id;
        $active->city_id = $owner->city_id;
        $active->action_id = $last_id->id;
        $active->action_type = 'customer_add';
        $active->action = "Mulk egasi qo'shildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        echo $customer->id;
    }

    public function index(Request $request)
    {
        $page = $request->query('page', 1);
        $customersList = new AgrotehApiModel();
        $customers = json_decode(json_encode($customersList->CustomersList($page)['customers']), FALSE);
        $totalResults =$customers->total;
        $perPage = 50;
        $pagination = new LengthAwarePaginator(
            $customers->data,
            $totalResults,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
        return view('customer.list', [
            'customers' =>$pagination,
        ]);
    }
    public function show($id)
    {
        $viewid = $id;
        $customersList = new AgrotehApiModel();
        $customer = json_decode(json_encode($customersList->CustomerView($id)['customer']), FALSE);
        $title = $customer->name . ' ' . $customer->lastname;
        $transports = $customersList->CustomerView($id)['transports'];
        if($transports)
        {
            foreach ($transports as $key=>$value){
               $transports[$key]['inspection'] = $value['tex-osmotr'];
                $transports[$key]['inspection_date'] = $value['tex-osmotr-date'];
            }
        }
        $transports = json_decode(json_encode($transports), FALSE);
        return view('customer.view', compact('customer', 'transports',  'title'));
    }

    public function customershow($id)
    {
        $viewid = $id;
        $customersList = new AgrotehApiModel();
        $customer = $customersList->CustomerView($id)->customer;

        $title = $customer->name . ' ' . $customer->lastname;
        if ($customer->residence == 1) {
            $passport_given_city = DB::table('tbl_cities')->where('id', '=', $customer->p_given_city)->first();
        } else {
            $passport_given_city = $customer->p_given_city;
        }
        if ($customer->type == 'legal' && $customer->filial_of) {
            $customer->filial_of = DB::table('customers')->select('id', 'name')->where('id', '=', $customer->filial_of)->first();
        }

//        $transports_by_type = DB::table('tbl_vehicles')
//            ->select(DB::raw('COUNT(tbl_vehicles.id) as c'), 'tbl_vehicle_types.vehicle_type')
//            ->join('tbl_vehicle_brands', 'tbl_vehicle_brands.id', '=', 'tbl_vehicles.vehiclebrand_id')
//            ->join('tbl_vehicle_types', 'tbl_vehicle_types.id', '=', 'tbl_vehicle_brands.vehicle_id')
//            ->where('owner_id', '=', $customer->id)
//            ->groupBy('tbl_vehicle_brands.vehicle_id')
//            ->get()->toArray();
        $transports = optional($customersList->CustomerView($id))->transports;

//        foreach ($transports as $transport) {
//            if ($transport->main_type == 'agregat') {
//                $certificate = DB::table('vehicle_certificates')
//                    ->where('vehicle_id', '=', $transport->id)
//                    ->where('owner_id', '=', $transport->owner_id)
//                    ->where('status', '=', 'active')
//                    ->latest()
//                    ->first();
//                $transport->certificate_series = $certificate ? $certificate->series : '';
//                $transport->certificate_number = $certificate ? $certificate->number : '';
//            } else {
//                $passport = DB::table('technical_passports')
//                    ->where('vehicle_id', '=', $transport->id)
//                    ->where('owner_id', '=', $transport->owner_id)
//                    ->where('status', '=', 'active')
//                    ->latest('given_date')
//                    ->first();
//                $transport->passport_series = empty($passport) ? '' : $passport->series;
//                $transport->passport_number = empty($passport) ? '' : $passport->number;
//            }
//            $inspection = VehicleInspection::mandatory()
//                ->where('vehicle_inspections.vehicle_id', $transport->id)
//                ->where('vehicle_inspections.owner_id', $customer->id)
//                ->orderBy('vehicle_inspections.date', 'DESC')
//                ->first();
//            if (!empty($inspection)) {
//                $transport->inspection_date = $inspection->date;
//            } else {
//                $transport->inspection_date = '';
//            }
//        }

        // preparing actions for giving transport numbers
//        $transport_numbers = DB::table('transport_numbers')
//            ->join('tbl_vehicles', 'tbl_vehicles.id', '=', 'transport_numbers.vehicle_id')
//            ->join('tbl_vehicle_brands', 'tbl_vehicle_brands.id', '=', 'tbl_vehicles.vehiclebrand_id')
//            ->join('tbl_vehicle_types', 'tbl_vehicle_types.id', '=', 'tbl_vehicle_brands.vehicle_id')
//            ->where('transport_numbers.owner_id', '=', $id)
//            ->select(
//                'transport_numbers.*',
//                'tbl_vehicle_types.vehicle_type as vehicle_type',
//                'tbl_vehicle_brands.vehicle_brand as model'
//            )->latest()->get()->toArray();
//
//        //preparing actions for technical passports
//        $technical_passports = DB::table('technical_passports')
//            ->join('tbl_vehicles', 'tbl_vehicles.id', '=', 'technical_passports.vehicle_id')
//            ->join('tbl_vehicle_brands', 'tbl_vehicle_brands.id', '=', 'tbl_vehicles.vehiclebrand_id')
//            ->join('tbl_vehicle_types', 'tbl_vehicle_types.id', '=', 'tbl_vehicle_brands.vehicle_id')
//            ->where('technical_passports.owner_id', '=', $id)
//            ->select(
//                'technical_passports.*',
//                'tbl_vehicle_types.vehicle_type as vehicle_type',
//                'tbl_vehicle_brands.vehicle_brand as model'
//            )->latest()->get()->toArray();

//        foreach ($technical_passports as $tP) {
//            $transport_number = getTransportNumberByPassport($tP);
//            if ($transport_number) {
//                $tP->number_code = $transport_number->code;
//                $tP->number_series = $transport_number->series;
//                $tP->number_number = $transport_number->number;
//            } else {
//                $tP->number_code = '';
//                $tP->number_series = '';
//                $tP->number_number = '';
//            }
//        }

        //preparing actions for certificates
        $certificates = DB::table('vehicle_certificates')
            ->join('tbl_vehicles', 'tbl_vehicles.id', '=', 'vehicle_certificates.vehicle_id')
            ->join('tbl_vehicle_brands', 'tbl_vehicle_brands.id', '=', 'tbl_vehicles.vehiclebrand_id')
            ->join('tbl_vehicle_types', 'tbl_vehicle_types.id', '=', 'tbl_vehicle_brands.vehicle_id')
            ->where('vehicle_certificates.owner_id', '=', $id)
            ->select(
                'vehicle_certificates.*',
                'tbl_vehicle_types.vehicle_type as vehicle_type',
                'tbl_vehicle_brands.vehicle_brand as model'
            )->latest()->get()->toArray();

        //preparing actions for driver licences
        $driver_licences = DB::table('driver_licences')
            ->join('customers', 'customers.id', '=', 'driver_licences.owner_id')
            ->where('driver_licences.owner_id', '=', $customer->id)
            ->select(
                'driver_licences.*',
                'driver_licences.type as licence_type'
            );
        $active_driver_licence = $driver_licences->where('driver_licences.status', '=', 'active')->first();

        $driver_licences = $driver_licences->get()->toArray();

        // preparing actions for registering/unregistering vehicles
        $registrations = DB::table('vehicle_registrations')
            ->join('tbl_vehicles', 'tbl_vehicles.id', '=', 'vehicle_registrations.vehicle_id')
            ->join('tbl_vehicle_brands', 'tbl_vehicle_brands.id', '=', 'tbl_vehicles.vehiclebrand_id')
            ->join('tbl_vehicle_types', 'tbl_vehicle_types.id', '=', 'tbl_vehicle_brands.vehicle_id')
            ->join('tbl_cities', 'tbl_cities.id', '=', 'vehicle_registrations.city_id')
            ->join('tbl_states', 'tbl_states.id', '=', 'tbl_cities.state_id')
            ->where('vehicle_registrations.owner_id', '=', $customer->id)
            ->select(
                'vehicle_registrations.*',
                'tbl_vehicles.type as main_type',
                'tbl_vehicle_types.vehicle_type as vehicle_type',
                'tbl_vehicle_brands.vehicle_brand as model',
                'tbl_cities.name as city',
                'tbl_states.name as state'
            )->latest()
            ->get()->toArray();

        foreach ($registrations as $reg) {
            $reg->number_code = '';
            $reg->number_series = '';
            $reg->number_number = '';
            if ($reg->action == 'regged') {
                $number = DB::table('transport_numbers')
                    ->where('transport_numbers.owner_id', '=', $reg->owner_id)
                    ->where('transport_numbers.vehicle_id', '=', $reg->vehicle_id)
                    ->whereDate('transport_numbers.given_date', '>=', $reg->date)
                    ->orderBy('transport_numbers.given_date')
                    ->first();

                $lastRegistration = DB::table('vehicle_registrations')
                    ->join('customers', 'customers.id', '=', 'vehicle_registrations.owner_id')
                    ->where('vehicle_registrations.vehicle_id', '=', $reg->vehicle_id)
                    ->where('vehicle_registrations.action', '=', 'unregged')
                    ->whereDate('vehicle_registrations.date', '<=', $reg->date)
                    ->orderBy('vehicle_registrations.date', 'DESC')
                    ->select(
                        'vehicle_registrations.*',
                        'customers.name as last_owner_name',
                        'customers.middlename as last_owner_middlename',
                        'customers.lastname as last_owner_lastname'
                    )->first();
            } elseif ($reg->action == 'unregged') {
                $number = DB::table('transport_numbers')
                    ->where('transport_numbers.owner_id', '=', $reg->owner_id)
                    ->where('transport_numbers.vehicle_id', '=', $reg->vehicle_id)
                    ->whereDate('transport_numbers.given_date', '<=', $reg->date)
                    ->orderBy('transport_numbers.given_date', 'DESC')
                    ->first();

                $nextRegistration = DB::table('vehicle_registrations')
                    ->join('customers', 'customers.id', '=', 'vehicle_registrations.owner_id')
                    ->join('tbl_vehicles', 'tbl_vehicles.id', '=', 'vehicle_registrations.vehicle_id')
                    ->where('vehicle_registrations.vehicle_id', '=', $reg->vehicle_id)
                    ->where('vehicle_registrations.action', '=', 'regged')
                    ->whereDate('vehicle_registrations.date', '>=', $reg->date)
                    ->where('tbl_vehicles.status', '=', 'regged')
                    ->orderBy('vehicle_registrations.date', 'ASC')
                    ->select(
                        'vehicle_registrations.*',
                        'customers.name as next_owner_name',
                        'customers.middlename as next_owner_middlename',
                        'customers.lastname as next_owner_lastname',
                        'customers.type as next_owner_type'
                    )->first();
            }

            if ($number) {
                $reg->number_code = $number->code;
                $reg->number_series = $number->series;
                $reg->number_number = $number->number;
            }

            if (!empty($lastRegistration)) {
                $reg->last_owner_id = $lastRegistration->owner_id;
                $reg->last_owner_name = $lastRegistration->last_owner_name;
                $reg->last_owner_middlename = $lastRegistration->last_owner_middlename;
                $reg->last_owner_lastname = $lastRegistration->last_owner_lastname;
                $reg->last_reg_date = $lastRegistration->date;
            }

            if (!empty($nextRegistration)) {
                $reg->next_owner_id = $nextRegistration->owner_id;
                $reg->next_owner_name = $nextRegistration->next_owner_name;
                $reg->next_owner_middlename = $nextRegistration->next_owner_middlename;
                $reg->next_owner_lastname = $nextRegistration->next_owner_lastname;
                $reg->next_reg_date = $nextRegistration->date;
                $reg->next_owner_type = $nextRegistration->next_owner_type;
            }
        }

        return view('customer.view', compact('customer', 'viewid', 'passport_given_city', 'transports_by_type', 'transports', 'transport_numbers', 'technical_passports', 'certificates', 'driver_licences', 'active_driver_licence', 'registrations', 'title'));
    }



    // customer delete
    public function destroy($id){
        $customer = DB::table('customers')->where('id','=',$id)->delete();

        $tbl_incomes = DB::table('tbl_incomes')->where('customer_id','=',$id)->delete();

        $tbl_invoices = DB::table('tbl_invoices')->where('customer_id','=',$id)->delete();

        $tbl_jobcard_details = DB::table('tbl_jobcard_details')->where('customer_id','=',$id)->delete();

        return redirect('/customer/list')->with('message','Successfully Deleted');

    }





    //  Add customer category

    public function categoryadd(Request $request){

        $customer_category=$request->input('customer_category');



        $count = DB::table('customer_categories')->where('name','=',$customer_category)->count();



        if ($count==0){

            $cat = new CustomerCategories;

            $cat->name = $customer_category;

            $cat->save();

            echo $cat->id;

        }

        else{

            return "01";

        }

    }



    // DElete Customer category
    public function categorydelete(Request $request){

        $id = $request->input('categoryid');

        DB::table('customer_categories')->where('id','=',$id)->delete();

    }



    public function categories(){

        $title="Mulk egalari kategoriyalari";

        $customerCategories=DB::table('customer_categories')->get()->toArray();

        return view('customer-category.list',compact('title','customerCategories'));

    }

    public function category_add(){
        $title="Mulk egasi kategoriyasi qo'shish";
        return view('customer-category.add',compact('title'));
    }

    public function category_store(Request $request){
        $name=$request->input('name');

        $count = DB::table('customer_categories')->where('name','=',$name)->count();
        if ($count==0){
            $cat = new CustomerCategories;
            $cat->name = $name;
            $cat->save();
            return redirect('customer/category/list')->with('message','Successfully Submitted');
        }else{
            return redirect('customer/category/add')->with('message','Duplicate Data');
        }
    }

    public function category_edit($id){
        $title="Mulk egasi kategoriyasini tahrirlash";
        $cat=CustomerCategories::find($id);
        return view('customer-category.edit',compact('title','cat'));
    }

    public function category_update(Request $request){
        $name=$request->input('name');
        $id=$request->input('id');
        $count = DB::table('customer_categories')->where('name','=',$name)->count();
        if ($count==0){
            $cat = CustomerCategories::find($id);
            $cat->name = $name;
            $cat->save();
            return redirect('customer/category/list')->with('message','Successfully Submitted');
        }else{
            return redirect('customer/category/edit/'.$id)->with('message','Duplicate Data');
        }
    }

    //  Add Ownership form
    public function ownershipformadd(Request $request){

        $ownershipForm=$request->input('ownershipForm');



        $count = DB::table('ownership_forms')->where('name','=',$ownershipForm)->count();



        if ($count==0){

            $cat = new OwnershipForm;

            $cat -> name = $ownershipForm;

            $cat -> save();

            echo $cat->id;

        }

        else{

            return "01";

        }

    }



    // DElete Ownership form

    public function ownershipformdelete(Request $request){

        $id = $request->input('ownershipFormId');
        $customers = DB::table('customers')->where('form', '=', $id)->get()->toArray();
        if(empty($customers)){
            DB::table('ownership_forms')->where('id','=',$id)->delete();
        }else{
            echo "error";
        }



    }

    // customer update
    public function customerupdate($id, StoreRequest $request)
    {
        /* @var Customer $customer */
        $customer = Customer::findOrFail($id);
        $customer->fill($request->validated());

        $customer->name=$request->input('name');
        $customer->lastname = $request->input('lastname');
        $customer->middlename = $request->input('middlename');
        $customer->d_o_birth = date('Y-m-d', strtotime($request->input('dob')));

        $customer->type = $request->input('customer_type');
        $customer->filial_of = $request->input('filial_of');

        $customer->form = $request->input('ownership_form');
        $customer->category = $request->input('category');

        $customer->id_number = $request->input('id_number');
        $customer->passport_series = $request->input('passport_series');
        $customer->passport_number = $request->input('passport_number');
        $customer->p_given_date = date('Y-m-d', strtotime($request->input('p_given_date')));
        $customer->p_given_city = $request->input('p_given_city');

        $customer->mobile = cleanPhone($request->input('mobile'));
        $customer->email = $request->input('email');
        $customer->city_id = $request->input('city');
        $customer->address = $request->input('address');

        $changedLocation = $customer->isDirty(['city_id']);
        $customer->save();

        if ($changedLocation) {
            $customer->vehicles()->update(['area_id' => $customer->city_id]);
        }
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $customer->id;
        $active->user_id = Auth::user()->id;
        $active->city_id = $customer->city_id;
        $active->action_id = $customer->id;
        $active->action_type = 'customer_edit';
        $active->action = "Mulk egasi malumoti yangilandi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        echo 'success';
    }

    public function transport_number_form(Request $request)
    {
        $vehicle = null;
        $customer = null;

        $title = "Davlat raqami berish";
        $min = DB::table('tbl_payment_types')->where('category', '=', 'min')->get()->first();
        $payment_n = DB::table('tbl_payment_types')->where([['category', '=', 'vehicle_num'], ['code', '=', 'new']])->get()->toArray();
        $payment_r = DB::table('tbl_payment_types')->where([['category', '=', 'vehicle_num'], ['code', '=', 'rec']])->get()->toArray();
        $documents = DB::table('documents')->where('service', '=', 'number')->orderBy('name')->get()->toArray();


        $states = Region::where('country_id', '=', 234)
            ->when(auth()->user()->region_ids, function ($query) {
                $query->whereIn('id', auth()->user()->region_ids);
            })
            ->orderBy('name')
            ->get();

        $vehicle_id = $request->input('vehicle_id');

        if ($vehicle_id) {

            $vehicle = DB::table('tbl_vehicles')->where('id', '=', $vehicle_id)->first();

            $customer = DB::table('customers')
                ->join('ownership_forms', 'ownership_forms.id', '=', 'customers.form')
                ->join('tbl_cities', 'tbl_cities.id', '=', 'customers.city_id')
                ->where('customers.id', $vehicle->owner_id)
                ->select('customers.*', 'ownership_forms.name as ownership_form', 'tbl_cities.state_id')
                ->first();
        } else {
            $vehicle = null;
        }
        return view('transport-number.oldform', compact(
            'states', 'vehicle', 'customer', 'title', 'min', 'payment_n', 'payment_r', 'documents'
        ));
    }

    public function transport_number_store(Request $request){

        $action=$request->input('action');

        $vehicleId=$request->input('transport');

        $totalAmount=$request->input('payment');

        $paymentDate=null;

        $paymentStatus='paid';
        $paymentDate=date('Y-m-d');


        if($action=='recover'){

            DB::table('transport_numbers')
                ->where('vehicle_id','=',$vehicleId)
                ->where('status','=','active')
                ->update(['status'=>'inactive']);

            DB::table('technical_passports')
                ->where('vehicle_id','=',$vehicleId)
                ->where('status','=','active')
                ->update(['status'=>'inactive']);

            DB::table('vehicle_certificates')
                ->where('vehicle_id','=',$vehicleId)
                ->where('status','=','active')
                ->update(['status'=>'inactive']);

        }elseif($action=='give'){

            $activeTNumbers=DB::table('transport_numbers')

                ->where('vehicle_id','=',$vehicleId)

                ->where('status','=','active')

                ->count();

            if($activeTNumbers){

                return 'active-transport-number-exists';

            }

        }



        $tNumber=new TransportNumber;
        $tNumber->vehicle_id=$vehicleId;
        $tNumber->owner_id=$request->input('customer_id');
        $tNumber->type=$request->input('type');
        $tNumber->series=$request->input('series');
        $tNumber->number=$request->input('number');
        $tNumber->code=$request->input('code');
        $tNumber->total_amount=$totalAmount;
        $tNumber->paid_amount=$totalAmount;
        $tNumber->given_date=join('-',array_reverse(explode('-',$request->input('given_date'))));
        $tNumber->action=$action;
        $tNumber->recover_reason=$request->input('recover_reason');
        $tNumber->doc=$request->input('doc');
        $tNumber->doc_note=$request->input('doc-note');
        $tNumber->status='active';
        $tNumber->payment_status=$paymentStatus;
        $tNumber->payment_date=$paymentDate;
        $tNumber->state_id=$request->input('state_id');
        $tNumber->user_id=Auth::user()->id;
        $tNumber->save();
        $last_id = DB::table('transport_numbers')->orderBy('id', 'desc' )->get()->first();
        $owner = DB::table('customers')->where('id', '=', $request->input('customer_id'))->get()->first();
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $owner->id;
        $active->user_id = Auth::user()->id;
        $active->city_id = $owner->city_id;
        $active->vehicle_id = $vehicleId;
        $active->action_id = $last_id->id;
        $active->action_type = 'technical_num';
        $active->action = "Davlat raqami berildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        echo 'success';
    }

    public function transport_numbers(Request $request)
    {
        $from = $request->input('from');
        $till = $request->input('till');
        $user = auth()->user();

        $numbers = \App\Models\TransportNumber::with(['customer.area.region', 'vehicle.brand.vehicleType', 'region', 'document']);

        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $numbers = $numbers->whereDate('given_date', '>=', $fromTime)
                ->whereDate('given_date', '<=', $tillTime);
        }

        $numbers->whereHas('customer', function ($query) use ($user) {
            if (!$user->role == 'admin') {
                $query->whereIn('city_id', LocationService::getAllowedAreas($user)->pluck('id'));
            }
        });

        $numbers->when(request()->input('s'), function ($query, $search) {
            $query->whereHas('customer', function ($query) use ($search) {
                if (is_numeric($search)) {
                    $query->orWhere('inn', $search)->orWhere('pinfl', $search);
                } else {
                    $query->where('f_name', 'like', "%$search%");
                }
            })
                ->orWhere('full_number', $search)
                ->orWhereHas('vehicle', function ($query) use ($search) {
                    $query->whereRaw("'$search' in (engineno, corpusno, chassisno)");
                });
        });

        $numbers = $numbers->latest()->paginate(50);

        if ($s = request()->input('s')) {
            $numbers->appends(['s' => $s]);
        }

        $datetime1 = new DateTime(date('d-m-Y'));
        foreach ($numbers as $driver_lic) {
            $datetime2 = new DateTime(date('d-m-Y', strtotime($driver_lic->created_at)));
            $interval = $datetime1->diff($datetime2);
            $driver_lic->day = $interval->format('%a');
        }

        return view('transport-number.list', compact('numbers', 'from', 'till'));
    }

    public function transport_number_cancel($id){
        $t_number = DB::table('transport_numbers')->where('id', '=', $id)->get()->first();
        $owner = DB::table('customers')->where('id', '=', $t_number->owner_id)->get()->first();
        $t_vehicle = DB::table('tbl_vehicles')->where('id', '=', $t_number->vehicle_id)->get()->first();
        if(!empty($t_vehicle) && $t_vehicle->type == 'vehicle' || !empty($t_vehicle) && $t_vehicle->type == 'tirkama'){
            DB::table('technical_passports')->where([['vehicle_id', '=', $t_vehicle->id], ['status', '=', 'active']])->delete();
        }elseif(!empty($t_vehicle) && $t_vehicle->type == 'agregat'){
            DB::table('vehicle_certificates')->where([['vehicle_id', '=', $t_vehicle->id], ['status', '=', 'active']])->delete();
        }

        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $owner->id;
        $active->user_id = Auth::user()->id;
        $active->city_id = $owner->city_id;
        $active->vehicle_id = $t_number->vehicle_id;
        $active->action_id = $t_number->id;
        $active->action_type = 'technical_num_c';
        $active->action = "Davlat raqami bekor qlindi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        DB::table('transport_numbers')->where('id', '=', $id)->delete();
        return redirect('/vehicle/transport-number/list')->with('message','Successfully Deleted');
    }

    public function transport_number_preview(Request $request){
        $title="Davlat raqami";
        $transportNumberId=$request->input('id');
        if($transportNumberId){
            $transportNumber=DB::table('transport_numbers')
                ->join('tbl_vehicles','tbl_vehicles.id','=','transport_numbers.vehicle_id')
                ->join('tbl_vehicle_brands','tbl_vehicle_brands.id','=','tbl_vehicles.vehiclebrand_id')
                ->join('tbl_vehicle_types','tbl_vehicle_types.id','=','tbl_vehicle_brands.vehicle_id')
                ->join('vehicle_works_fors','vehicle_works_fors.id','=','tbl_vehicle_brands.working_type_id')
                ->leftJoin('tbl_fuel_types','tbl_fuel_types.id','=','tbl_vehicles.fuel_id')
                ->leftJoin('tbl_colors','tbl_colors.id','=','tbl_vehicles.color_id')
                ->join('customers','customers.id','=','transport_numbers.owner_id')
                ->join('tbl_cities','tbl_cities.id','=','customers.city_id')
                ->join('tbl_states','tbl_states.id','=','tbl_cities.state_id')
                ->where('transport_numbers.id','=',$transportNumberId)
                ->select(
                    'transport_numbers.*',
                    'tbl_vehicles.modelyear',
                    'tbl_vehicles.corpusno',
                    'tbl_vehicles.chassisno',
                    'tbl_vehicles.engineno',
                    'tbl_vehicles.condition',
                    'tbl_vehicles.factory_number',
                    'vehicle_works_fors.name as working_type',
                    'tbl_vehicle_types.vehicle_type as vehicle_type',
                    'tbl_vehicle_brands.vehicle_brand as vehicle_brand',
                    'tbl_fuel_types.name as fuel_type',
                    'tbl_colors.color',
                    'customers.name as owner_name',
                    'customers.lastname as owner_lastname',
                    'customers.middlename as owner_middlename',
                    'customers.id_number',
                    'customers.inn',
                    'customers.pinfl',
                    'customers.address',
                    'customers.type as owner_type',
                    'customers.passport_series',
                    'customers.passport_number',
                    'tbl_cities.name as city',
                    'tbl_states.name as state'
                )->first();

            if($transportNumber){
                $transportNumber->doc=getDocumentName($transportNumber->doc);
            }

            if($request->input('print')){
                $print=true;
            }

            if($request->input('details')){
                $view='transport-number.preview-details';
            }else{
                $view='transport-number.preview-details';
            }
            return view($view,compact('transportNumber','title','transportNumberId','print','details'));
        }else{
            return 'no-id-provided';
        }
    }

    public function check_transport_number(Request $request){
        $code=$request->input('code');
        $series=$request->input('series');
        $number=$request->input('number');
        $type = $request->input('type');

        $trNumber=DB::table('transport_numbers')
            ->where('status','=','active')
            ->where('code','=',$code)
            ->where('series','=',$series)
            ->where('number','=',$number)
            ->where('type', '=', $type)
            ->count();

        if($trNumber){

            return 'exist';

        }else{

            return 'not-exist';

        }

    }

    public function get_last_tr_number(Request $request){
        $vehicleId=$request->input('vehicle_id');
        return getLastActiveTransportNumber($vehicleId);
    }

    public function checkIdentificator(Request $request){
        switch ($request->input('field')) {
            case 'inn':
                $customer = Customer::where('inn', $request->input('id'))->where('filial_of', '=', 0)->first();
                break;
            case 'pinfl':
                $customer = Customer::where('pinfl', $request->input('id'))->first();
                break;
            default:
                $customer = Customer::where('id_number', $request->input('id'))->first();
                break;
        }

        return [
            'exist' => boolval($customer),
            'owner' => $customer,
        ];
    }

    public function technical_passport_form(Request $request){

        $title="Texnik passport berish";
        $doc='passport';
        $seriesNumber=generateSeriesNumber('technical_passports');
        $payment_r = DB::table('tbl_payment_types')->where([['category', '=', 'vehicle_pass'], ['code', '=', 'rec']])->get();
        $payment_n = DB::table('tbl_payment_types')->where([['category', '=', 'vehicle_pass'], ['code', '=', 'new']])->get()->first();
        $min = DB::table('tbl_payment_types')->where('category', '=', 'min')->get()->first();
        $documents=DB::table('documents')->where('service','=','technical-passport')->orderBy('name')->get()->toArray();

        $vehicle_id=$request->input('vehicle_id');

        if($vehicle_id){
            $vehicle=DB::table('tbl_vehicles')->where('id','=',$vehicle_id)->first();
            $customer=DB::table('customers')
                ->join('ownership_forms','ownership_forms.id','=','customers.form')
                ->where('customers.id',$vehicle->owner_id)
                ->select('customers.*','ownership_forms.name as ownership_form')
                ->first();
        } else {
            $vehicle = null;
            $customer = null;
        }

        return view('technical-passport.old_form',compact('title','seriesNumber','vehicle','customer','doc', 'payment_r', 'payment_n', 'min','documents'));
    }

    public function technical_passport_store(Request $request){
        $doc=$request->input('doc');
        if($doc=='passport'){
            $series = $request->input('series');
            $number = $request->input('number');
            $action=$request->input('action');
            $tPassportId=$request->input('technical-passport-id');
            $vehicleId=$request->input('transport');
            $totalAmount=$request->input('payment');
            $paymentStatus='unpaid';
            $paymentDate=null;
            $paymentStatus='paid';
            $paymentDate=date('Y-m-d');
            if(!$tPassportId){
                if($action=='recover' || $request->input('source-doc') == 9){
                    DB::table('technical_passports')
                        ->where('vehicle_id','=',$vehicleId)
                        ->where('status','=','active')
                        ->update(['status'=>'inactive']);
                }elseif($action=='give' && $request->input('source-doc') == 9){
                    $activeTNumbers=DB::table('technical_passports')
                        ->where('vehicle_id','=',$vehicleId)
                        ->where('status','=','active')
                        ->count();
                    if($activeTNumbers){
                        return json_encode(['message'=>'active-technical-passport-exists']);
                    }
                }

                $tPassport = new TechnicalPassport;
            }else{
                $tPassport=TechnicalPassport::find($tPassportId);
            }
            $tPassport->vehicle_id=$vehicleId;
            $tPassport->owner_id=$request->input('customer_id');
            $tPassport->user_id=Auth::user()->id;
            $tPassport->doc=$request->input('source-doc');
            $tPassport->doc_note=$request->input('doc-note');
            $t_passport = DB::table('technical_passports')->where([['series', '=', $series], ['number', '=', $number]])->get()->first();
            if(!empty($t_passport) && $request->input('source-doc') != 18 && !$tPassportId){
                $seriesNumber=generateSeriesNumber('technical_passports');
                $tPassport->series=$seriesNumber->series;
                $tPassport->number=$seriesNumber->number;
            }else{
                $tPassport->series=$series;
                $tPassport->number=$number;
            }
            if($request->input('source-doc') == 18){
                $tPassport->series=$series;
                $tPassport->number=$number;
            }
            $tPassport->total_amount=$totalAmount;
            $tPassport->paid_amount=$totalAmount;
            $tPassport->given_date=join('-',array_reverse(explode('-',$request->input('given_date'))));
            $tPassport->action=$action;
            $tPassport->payment_status=$paymentStatus;
            $tPassport->payment_date=$paymentDate;
            $tPassport->status='active';
            $tPassport->note=$request->input('note');
            $tPassport->recover_reason=$request->input('recover_reason');
            $tPassport->save();
            // saving to activities
            $last_id = DB::table('technical_passports')->where('id', '=', $tPassport->id)->get()->first();
            $owner = DB::table('customers')->where('id', '=', $request->input('customer_id'))->get()->first();
            $active = new tbl_activities;
            $active->ip_adress = $_SERVER['REMOTE_ADDR'];
            $active->owner_id = $owner->id;
            $active->user_id = Auth::user()->id;
            $active->city_id = $owner->city_id;
            $active->vehicle_id = $vehicleId;
            $active->action_id = $last_id->id;
            $active->action_type = 'technical_pass';
            $active->action = "Texnika/Tirkama ga pasport berildi ".$last_id->series." ".$last_id->number;
            $active->time = date('Y-m-d H:i:s');
            $active->save();
            return json_encode(['id'=>$tPassport->id,'message'=>'success']);

        }elseif($doc=='certificate'){
            $series = $request->input('series');
            $number = $request->input('number');
            $t_certificate = DB::table('vehicle_certificates')->where([['series', '=', $series], ['number', '=', $number]])->get()->first();
            $action=$request->input('action');
            $certificateId=$request->input('vehicle-certificate-id');
            $vehicleId=$request->input('transport');
            $totalAmount=$request->input('payment');
            $paymentStatus='unpaid';
            $paymentDate=null;
            $paymentStatus='paid';
            $paymentDate=date('Y-m-d');
            if(!$certificateId){
                if($action=='recover' || $request->input('source-doc') == 14){
                    DB::table('vehicle_certificates')
                        ->where('vehicle_id','=',$vehicleId)
                        ->where('status','=','active')
                        ->update(['status'=>'inactive']);
                }elseif($action=='give' && $request->input('source-doc') == 14){
                    $activeTNumbers=DB::table('vehicle_certificates')
                        ->where('vehicle_id','=',$vehicleId)
                        ->where('status','=','active')
                        ->count();
                    if($activeTNumbers){
                        return json_encode(['message'=>'active-vehicle-certificate-exists']);
                    }
                }
                $certificate = new vehicle_certificates;
            }else{
                $certificate = vehicle_certificates::find($request->input('vehicle-certificate-id'));
            }
            $certificate->vehicle_id=$vehicleId;
            $certificate->owner_id=$request->input('customer_id');
            if(!empty($t_certificate) && $request->input('source-doc') != 30 && !$certificateId){
                $seriesNumber=generateSeriesNumber('vehicle_certificates');
                $certificate->series=$seriesNumber->series;
                $certificate->number=$seriesNumber->number;
            }else{
                $certificate->series = $series;
                $certificate->number = $number;
            }
            if($request->input('source-doc') == 30){
                $certificate->series = $series;
                $certificate->number = $number;
            }
            $certificate->total_amount=$totalAmount;
            $certificate->paid_amount=$totalAmount;
            $certificate->given_date=join('-',array_reverse(explode('-',$request->input('given_date'))));
            $certificate->action=$action;
            $certificate->doc=$request->input('source-doc');
            $certificate->doc_note=$request->input('doc-note');
            $certificate->payment_status=$paymentStatus;
            $certificate->payment_date=$paymentDate;
            $certificate->status='active';
            $certificate->note=$request->input('note');
            $certificate->recover_reason=$request->input('recover_reason');
            $certificate->user_id=Auth::user()->id;
            $certificate->save();
            $last_id = DB::table('vehicle_certificates')->where('id', '=', $certificate->id)->get()->first();
            $owner = DB::table('customers')->where('id', '=', $request->input('customer_id'))->get()->first();
            $active = new tbl_activities;
            $active->ip_adress = $_SERVER['REMOTE_ADDR'];
            $active->owner_id = $owner->id;
            $active->user_id = Auth::user()->id;
            $active->city_id = $owner->city_id;
            $active->vehicle_id = $vehicleId;
            $active->action_id = $last_id->id;
            $active->action_type = 'vehicle_cer';
            $active->action = "Agregatga guvohnoma berildi ".$last_id->series." ".$last_id->number;
            $active->time = date('Y-m-d H:i:s');
            $active->save();
            return json_encode(['id'=>$certificate->id,'message'=>'success']);
        }
    }
    public function technical_passport_cancel($id){
        $tech = DB::table('technical_passports')->where('id', '=', $id)->get()->first();
        $owner = DB::table('customers')->where('id', '=', $tech->owner_id)->get()->first();
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $owner->id;
        $active->user_id = Auth::user()->id;
        $active->city_id = $owner->city_id;
        $active->vehicle_id = $tech->vehicle_id;
        $active->action_id = $id;
        $active->action_type = 'technical_pass_c';
        $active->action = "Texnika/Tirkama pasporti bekor qilindi ".$tech->series." ".$tech->number;
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        DB::table('technical_passports')->where('id', '=', $id)->delete();
        return redirect('/vehicle/technical-passport/list')->with('message','Successfully Deleted');
    }

    public function technical_passports(Request $request)
    {
        $title = "Texnik pasportlar";

        $from = $request->input('from');
        $till = $request->input('till');

        $passports = \App\Models\TechnicalPassport::with([
            'customer.area.region',
            'vehicle.brand.vehicleType',
            'transportNumber',
        ]);

        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $passports = $passports->whereDate('given_date', '>=', $fromTime)
                ->whereDate('given_date', '<=', $tillTime);
        }

        if (!auth()->user()->role == 'admin') {
            $passports = $passports->whereHas('customer', function ($query) {
                $query->whereIn('city_id', LocationService::getAllowedAreas(auth()->user())->pluck('id'));
            });
        }


        $passports->when(request()->input('s'), function ($query, $search) {
            $query->whereHas('customer', function ($query) use ($search) {
                if (is_numeric($search)) {
                    if (strlen($search) === 9) {
                        $query->orWhere('inn', $search);
                    } elseif (strlen($search) === 14) {
                        $query->orWhere('pinfl', $search);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                } else {
                    $query->where('f_name', 'like', "%$search%");
                }
            })
                ->orWhere('passport_id', 'like', "%$search")
                ->orWhereHas('transportNumber', function ($query) use ($search) {
                    $query->where('full_number', $search);
                });
        });

        $passports = $passports->latest()->paginate(50)->appends(['s' => request()->input('s')]);

        $datetime1 = new DateTime(date('d-m-Y'));
        foreach ($passports as $driver_lic) {
            $datetime2 = new DateTime(date('d-m-Y', strtotime($driver_lic->created_at)));
            $interval = $datetime1->diff($datetime2);
            $driver_lic->day = $interval->format('%a');
        }

        return view('technical-passport.list', compact('title', 'passports', 'from', 'till'));
    }

    public function technical_passport_preview(Request $request){
        $title="Texnik passport chop etish";
        $technicalPassportId=$request->input('id');
        if($technicalPassportId){
            $technicalPassport = TechnicalPassport::join('tbl_vehicles','tbl_vehicles.id','=','technical_passports.vehicle_id')
                //->leftJoin('vehicle_registrations','vehicle_registrations.vehicle_id', '=', 'tbl_vehicles.id')
                ->leftjoin('vehicle_registrations', function ($join) {
                    $join->on('vehicle_registrations.vehicle_id', '=', 'tbl_vehicles.id')
                        ->where('vehicle_registrations.status', VehicleRegistration::STATUS_ACTIVE);
                })

                ->join('tbl_vehicle_brands','tbl_vehicle_brands.id','=','tbl_vehicles.vehiclebrand_id')
                ->join('tbl_vehicle_types','tbl_vehicle_types.id','=','tbl_vehicle_brands.vehicle_id')
                ->leftjoin('vehicle_works_fors','vehicle_works_fors.id','=','tbl_vehicle_brands.working_type_id')
                ->leftJoin('tbl_fuel_types','tbl_fuel_types.id','=','tbl_vehicles.fuel_id')
                ->leftJoin('tbl_colors','tbl_colors.id','=','tbl_vehicles.color_id')
                ->join('customers','customers.id','=','technical_passports.owner_id')
                ->join('tbl_cities','tbl_cities.id','=','customers.city_id')
                ->join('users','users.id','=','technical_passports.user_id')
                ->join('tbl_states','tbl_states.id','=','tbl_cities.state_id')
                ->where('technical_passports.id', $technicalPassportId)
                ->select(
                    'technical_passports.*',
                    'vehicle_registrations.id as has_registration',
                    'vehicle_registrations.is_temporary as has_temporary_registration',
                    'tbl_vehicles.modelyear',
                    'tbl_vehicles.corpusno',
                    'tbl_vehicles.chassisno',
                    'tbl_vehicles.engineno',
                    'tbl_vehicles.enginesize',
                    'tbl_vehicles.condition',
                    'tbl_vehicles.factory_number',
                    'tbl_vehicles.weight_full',
                    'tbl_vehicles.weight',
                    'tbl_vehicles.lising as is_lising',
                    'vehicle_works_fors.name as working_type',
                    'tbl_vehicles.type as vehicle_enum_type',
                    'tbl_vehicle_types.vehicle_type as vehicle_type',
                    'tbl_vehicle_brands.vehicle_brand as vehicle_brand',
                    'tbl_fuel_types.name as fuel_type',
                    'tbl_colors.color',
                    'customers.name as owner_name',
                    'customers.f_name',
                    'customers.lastname as owner_lastname',
                    'customers.middlename as owner_middlename',
                    'customers.id_number',
                    'customers.inn',
                    'customers.pinfl',
                    'customers.address',
                    'customers.type as owner_type',
                    'customers.passport_series',
                    'customers.passport_number',
                    'customers.form',
                    'tbl_cities.name as city',
                    'tbl_states.name as state',
                    'tbl_states.id as region',
                    'users.role',
                    'users.city_id as user_cities'
                )->first();

            if($technicalPassport){
                $technicalPassport->doc=getDocumentName($technicalPassport->doc);
            }else{
                return 'invalid id '.$request->input('id');
            }

            $transportNumber=getTransportNumberByPassport($technicalPassport);

            if(!empty($transportNumber)){
                $technicalPassport->n_code=$transportNumber->code;
                $technicalPassport->n_number=$transportNumber->number;
                $technicalPassport->n_series=$transportNumber->series;
            }else{
                $technicalPassport->n_code='';
                $technicalPassport->n_number='';
                $technicalPassport->n_series='';
            }

            if($technicalPassport && $technicalPassport->owner_type=='legal'){
                $technicalPassport->ownership_form=getOwnershipForm($technicalPassport->form);
            }

            if($request->input('print')){
                $print=true;
            }
            $passportId = $technicalPassportId;
            if($request->input('details')){
                $view='technical-passport.preview-details';
            }elseif ($request->input('show')){
                $view='technical-passport.preview';
            }else {
                return 'Not Found';
            }
            return view($view,compact('technicalPassport','title','technicalPassportId','print','passportId'));
        }else{
            return 'no-id-provided';
        }
    }

    public function vehicle_certificates(Request $request)
    {
        $from = $request->input('from');
        $till = $request->input('till');
        $searchQuery = $request->input('s');

        $certificatesQuery = VehicleCertificate::with(['customer.area.region', 'vehicle.brand.vehicleType'])
            ->when(!auth()->user()->role == 'admin', function ($query) use ($searchQuery) {
                $query->whereHas('customer', function ($customerQuery) use ($searchQuery) {
                    $customerQuery->whereIn('city_id', LocationService::getAllowedAreas(auth()->user())->pluck('id'));
                });
            });

        if ($from && $till) {
            $fromTime = join('-', array_reverse(explode('-', $from)));
            $tillTime = join('-', array_reverse(explode('-', $till)));
            $certificatesQuery->whereDate('given_date', '>=', $fromTime)
                ->whereDate('given_date', '<=', $tillTime);
        }

        if ($searchQuery) {
            $certificatesQuery->where(function ($query) use ($searchQuery) {
                $query->whereHas('customer', function ($customerQuery) use ($searchQuery) {
                    if (is_numeric($searchQuery)) {
                        $customerQuery->where('inn', $searchQuery);
                        $customerQuery->orWhere('pinfl', $searchQuery);
                    } else {
                        $customerQuery->where('f_name', 'like', "%$searchQuery%");
                    }
                })
                    ->orWhere('certificate_id', 'like', '%' . $searchQuery);
            });
        }


        $certificates = $certificatesQuery->latest()->paginate(50);

        if ($searchQuery) {
            $certificates->appends(['s' => $searchQuery]);
        }

        return view('certificate.list', compact('certificates', 'from', 'till'));
    }

    public function certificate_preview(Request $request){
        $title="Texnik guvohnoma chop etish";
        $certificateId=$request->input('id');
        if($certificateId){
            $certificate = VehicleCertificate::join('tbl_vehicles','tbl_vehicles.id','=','vehicle_certificates.vehicle_id')
                ->join('tbl_vehicle_brands','tbl_vehicle_brands.id','=','tbl_vehicles.vehiclebrand_id')
                ->join('tbl_vehicle_types','tbl_vehicle_types.id','=','tbl_vehicle_brands.vehicle_id')
                ->join('vehicle_works_fors','vehicle_works_fors.id','=','tbl_vehicle_brands.working_type_id')
                ->leftJoin('tbl_colors','tbl_colors.id','=','tbl_vehicles.color_id')
                ->leftJoin('tbl_fuel_types','tbl_fuel_types.id','=','tbl_vehicles.fuel_id')
                ->join('customers','customers.id','=','vehicle_certificates.owner_id')
                ->join('tbl_cities','tbl_cities.id','=','customers.city_id')
                ->join('tbl_states','tbl_states.id','=','tbl_cities.state_id')
                ->leftJoin('vehicle_registrations','vehicle_registrations.vehicle_id', '=', 'tbl_vehicles.id')
                ->where('vehicle_certificates.id','=',$certificateId)
                // ->where('vehicle_registrations.status', VehicleRegistration::STATUS_ACTIVE)
                ->select(
                    'vehicle_certificates.*',
                    'vehicle_registrations.is_temporary as has_temporary_registration',
                    'tbl_vehicles.modelyear',
                    'tbl_vehicles.corpusno',
                    'tbl_vehicles.chassisno',
                    'tbl_vehicles.engineno',
                    'tbl_vehicles.condition',
                    'tbl_vehicles.factory_number',
                    'tbl_vehicles.weight_full',
                    'tbl_vehicles.weight',
                    'tbl_vehicle_types.vehicle_type as vehicle_type',
                    'tbl_vehicle_brands.vehicle_brand as vehicle_brand',
                    'vehicle_works_fors.name as working_type',
                    'tbl_colors.color',
                    'tbl_fuel_types.name as fuel_type',
                    'customers.name as owner_name',
                    'customers.lastname as owner_lastname',
                    'customers.middlename as owner_middlename',
                    'customers.id_number',
                    'customers.inn',
                    'customers.pinfl',
                    'customers.address',
                    'customers.type as owner_type',
                    'customers.passport_series',
                    'customers.passport_number',
                    'customers.form',
                    'tbl_cities.name as city',
                    'tbl_states.id as region',
                    'tbl_states.name as state'
                )->first();

            if($certificate && $certificate->owner_type=='legal'){
                $certificate->ownership_form=getOwnershipForm($certificate->form);
            }

            if($certificate){
                $certificate->doc=getDocumentName($certificate->doc);
            }

            if($request->input('print')){
                $print=true;
            }
            if($request->input('details')){
                $view='certificate.preview-details';
            }elseif ($request->input('show')){
                $view='certificate.preview';
            } else {
                return 'Not Found';
            }
            return view($view,compact('certificate', 'title', 'certificateId', 'print', 'details'));
        }else{
            return 'no-id-provided';
        }
    }

    public function certificate_cancel(Request $request , $id){
        $tech = DB::table('vehicle_certificates')->where('id', '=', $id)->get()->first();
        $owner = DB::table('customers')->where('id', '=', $tech->owner_id)->get()->first();
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $owner->id;
        $active->user_id = Auth::user()->id;
        $active->city_id = $owner->city_id;
        $active->vehicle_id = $tech->vehicle_id;
        $active->action_id = $id;
        $active->action_type = 'vehicle_cer_c';
        $active->action = "Agregat guvohnomasi bekor qilindi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        DB::table('vehicle_certificates')->where('id', '=', $id)->delete();
        return redirect('/certificate/list')->with('message','Successfully Deleted');
    }

    public function driver_licence_form(Request $request){
        $settings = settings();
        if (!$settings->driver_licenses_enabled && !auth()->user()->role == 'admin') {
            return redirect('https://tm.uzagroteh.uz');
        }


        $user=Auth::user();
        $stateId=$user->state_id ? explode(',',$user->state_id)[0] : 4121; // tashkent city default
        $title="Haydovchilik guvohnomasi berish";

        $seriesNumber=generateSeriesNumber('driver_licences',$stateId);

        $customerId=$request->input('owner_id');
        $documents=DB::table('documents')->where('service','=','driver-license')->orderBy('name')->get()->toArray();
        $min = DB::table('tbl_payment_types')->where('category', '=', 'min')->get()->first();
        $payment_n = DB::table('tbl_payment_types')->where([['category', '=', 'driver_lic'], ['code', '=', 'new'], ['key_payment', '=', 'new']])->get()->first();
        $payment_u = DB::table('tbl_payment_types')->where([['category', '=', 'driver_lic'], ['code', '=', 'new'], ['key_payment', '=', 'update']])->get()->first();
        $payment_o = DB::table('tbl_payment_types')->where([['category', '=', 'driver_lic'], ['code', '=', 'new'], ['key_payment', '=', 'outof']])->get()->first();
        if($customerId){
            $customer=DB::table('customers')
                ->join('ownership_forms','ownership_forms.id','=','customers.form')
                ->where('customers.id',$customerId)
                ->select('customers.*','ownership_forms.name as ownership_form')
                ->first();
        } else {
            $customer = null;
        }

        return view('driver-licence.oldform',compact('title','seriesNumber','customer','documents', 'min', 'payment_n', 'payment_u', 'payment_o'));
    }

    public function driver_licence_store(Request $request){
        $settings = settings();
        if (!$settings->driver_licenses_enabled && !auth()->user()->role == 'admin') {
            return redirect('https://tm.uzagroteh.uz');
        }


        $user = Auth::user();
        $action=$request->input('action');
        $customerId=$request->input('customer_id');
        $totalAmount=$request->input('payment');

        $paymentStatus='paid';
        $paymentDate=date('Y-m-d');
        $note = $request->input('note');

        $dLicenseId = $request->input('driver-licence-id');
        $type=[];

        foreach($request->input('types') as $t){
            if(!empty($t) && !empty($t['name']) && !empty($t['date']) && !empty($t['duration'])){
                $type[]=['name'=>$t['name'],'given_date'=>$t['date'],'duration'=>$t['duration']];
            }
        }
        $series = $request->input('series');
        $number = $request->input('number');
        $local_series = $request->input('local_series');
        $local_number = $request->input('local_number');

        if($dLicenseId){
            $dLicence = DriverLicence::find($dLicenseId);
        }else{
            if($action=='recover' || $action=='update'){
                DB::table('driver_licences')
                    ->where('owner_id','=',$customerId)
                    ->where('status','=','active')
                    ->update(['status'=>'inactive']);
            }elseif($action=='give'){
                $activeDLicences=DB::table('driver_licences')
                    ->where('owner_id','=',$customerId)
                    ->where('status','=','active')
                    ->count();
                if($activeDLicences){
                    return json_encode(['message'=>'active-driver-licence-exists']);
                }
            }
            $dLicence = new DriverLicence;
        }
        $dLicence->note = $note;
        $dLicence->owner_id=$customerId;
        $dLicence->user_id=$user->id;
        $licenceold = DB::table('driver_licences')->where([['series', '=', $series], ['number', '=', $number]])->get()->first();
        if(!empty($licenceold) && !$dLicenseId){
            $stateId=$user->state_id ? explode(',',$user->state_id)[0] : 4121; // tashkent city default
            $seriesNumber=generateSeriesNumber('driver_licences', $stateId);
            $dLicence->series=$seriesNumber->series;
            $dLicence->number=$seriesNumber->number;
            $dLicence->local_series=$seriesNumber->local_series;
            $dLicence->local_number=$seriesNumber->local_number;
        }else{
            $dLicence->series=$series;
            $dLicence->number=$number;
            $dLicence->local_series=$local_series;
            $dLicence->local_number=$local_number;
        }

        // double checking for generated series and number, in order to eliminate duplication


        $dLicence->status='active';
        $dLicence->type=$type;
        $dLicence->action=$action;
        $dLicence->doc=$request->input('doc');
        $dLicence->doc_note=$request->input('doc-note');
        $dLicence->total_amount=$totalAmount;
        $dLicence->paid_amount=$totalAmount;
        $dLicence->given_date=$request->input('given_date')?$request->input('given_date'):date('Y-m-d');
        $dLicence->payment_status=$paymentStatus;
        $dLicence->payment_date=$paymentDate;
        $dLicence->recover_reason=$request->input('recover_reason');
        $dLicence->save();

        $owner = DB::table('customers')->where('id', '=', $customerId)->get()->first();
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $owner->id;
        $active->user_id = Auth::user()->id;
        $active->city_id = $owner->city_id;
        $active->action_id = $dLicence->id;
        $active->action_type = 'driver_lic';
        $active->action = "Haydovchilik guhohnoma berildi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();

        return json_encode(['message'=>'success','id'=>$dLicence->id]);
    }
    public function driver_licence_cancel($id){
        $lic = DB::table('driver_licences')->where('id', '=', $id)->get()->first();
        $owner = DB::table('customers')->where('id', '=', $lic->owner_id)->get()->first();
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $owner->id;
        $active->user_id = Auth::user()->id;
        $active->city_id = $owner->city_id;
        $active->action_id = $id;
        $active->action_type = 'driver_lic_c';
        $active->action = "Haydovchilik guvohnomasi bekor qilindi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        DB::table('driver_licences')->where('id', '=', $id)->delete();
        return redirect('/driver-licence/list')->with('message','Successfully Deleted');
    }


    public function driver_licence_image(Request $request){
        $driverId=$request->input('driverId');
        if($request->hasFile('image')) {
            $file = $request->file('image');

            //you also need to keep file extension as well
            $name = $file->getClientOriginalName().'.'.$file->getClientOriginalExtension();

            //using the array instead of object
            $image['filePath'] = $name;

            $dir=public_path().'/uploads/drivers/'.date('Y');
            if(!is_dir($dir)){
                mkdir($dir);
            }
            $dir=$dir.'/'.date('m');
            if(!is_dir($dir)){
                mkdir($dir);
            }
            $dir=$dir.'/'.date('d');
            if(!is_dir($dir)){
                mkdir($dir);
            }

            $file->move($dir, 'driver-'.$driverId.'.jpeg');
            echo '/public/uploads/drivers/'.date('Y').'/'.date('m').'/'.date('d').'/'.'driver-'.$driverId.'.jpeg?v='.strtotime('now');
        }else{
            echo 'else';
        }
    }

    public function driver_signature(Request $request){
        $driverId=$request->input('driverId');
        if($request->hasFile('image')) {
            $file = $request->file('image');

            //you also need to keep file extension as well
            $name = $file->getClientOriginalName().'.'.$file->getClientOriginalExtension();

            //using the array instead of object
            $image['filePath'] = $name;

            $dir=public_path().'/uploads/signatures/'.date('Y');
            if(!is_dir($dir)){
                mkdir($dir);
            }
            $dir=$dir.'/'.date('m');
            if(!is_dir($dir)){
                mkdir($dir);
            }
            $dir=$dir.'/'.date('d');
            if(!is_dir($dir)){
                mkdir($dir);
            }

            $file->move($dir, 'signature-driver-'.$driverId.'.png');
            echo '/public/uploads/signatures/'.date('Y').'/'.date('m').'/'.date('d').'/'.'signature-driver-'.$driverId.'.png?v='.strtotime('now');
        }else{
            echo 'else';
        }
    }

    public function driver_licence_preview(Request $request){
        $settings = settings();
        if (!$settings->driver_licenses_enabled && !auth()->user()->role == 'admin') {
            return redirect('https://tm.uzagroteh.uz');
        }


        $title="Traktorchi-mashinist guvohnomasini chop etish";

        $driverLicenceId=$request->input('id');
        if($driverLicenceId){
            $driverLicence = DriverLicence::query()
                ->join('customers','customers.id','=','driver_licences.owner_id')
                ->join('tbl_cities','tbl_cities.id','=','customers.city_id')
                ->join('tbl_states','tbl_states.id','=','tbl_cities.state_id')
                ->join('users','users.id','=','driver_licences.user_id')
                ->where('driver_licences.id','=',$driverLicenceId)
                ->select(
                    'driver_licences.*',
                    'customers.name as owner_name',
                    'customers.lastname as owner_lastname',
                    'customers.middlename as owner_middlename',
                    'customers.id_number',
                    'customers.inn',
                    'customers.pinfl',
                    'customers.address',
                    'customers.d_o_birth',
                    'customers.passport_number',
                    'customers.passport_series',
                    'customers.p_given_city',
                    'customers.address',
                    'users.city_id as given_city_id',
                    'tbl_cities.name as city',
                    'tbl_states.name as state'
                )->first();

            if($driverLicence){
                $driverLicence->doc = getDocumentName($driverLicence->doc);
            }

            if ($request->input('print')) {
                $print = true;
            } else {
                $print = false;
            }

            if ($request->input('details')) {
                $view = 'driver-licence.preview-details';
            } else {
                $view = 'driver-licence.preview2';
            }

            return view($view, compact('driverLicence', 'title', 'driverLicenceId', 'print'));
        }else{
            return 'no-id-provided';
        }
    }

    public function driver_exam_preview(Request $request){
        $title="Traktorchi-mashinist imtihonlari natijasi";
        $driverExamid=$request->input('id');
        if($driverExamid){
            $driverexam=DB::table('driver_exams')
                ->join('customers','customers.id','=','driver_exams.owner_id')
                ->join('tbl_cities','tbl_cities.id','=','customers.city_id')
                ->join('tbl_states','tbl_states.id','=','tbl_cities.state_id')
                ->join('driver_exam_types', 'driver_exam_types.id', '=', 'driver_exams.type')
                ->where('driver_exams.id','=',$driverExamid)
                ->select(
                    'driver_exams.*',
                    'customers.name as owner_name',
                    'customers.lastname as owner_lastname',
                    'customers.middlename as owner_middlename',
                    'customers.id_number',
                    'customers.inn',
                    'customers.pinfl',
                    'customers.address',
                    'customers.d_o_birth',
                    'customers.passport_number',
                    'customers.passport_series',
                    'customers.address',
                    'tbl_cities.name as city',
                    'tbl_states.name as state',
                    'driver_exam_types.name as examtype'
                )->first();
            return view('driver-exam.preview',compact('driverexam','title','driverExamid'));
        }else{
            return 'no-id-provided';
        }
    }

    public function driver_exam_cancel($id){
        $lic = DB::table('driver_exams')->where('id', '=', $id)->get()->first();
        $owner = DB::table('customers')->where('id', '=', $lic->owner_id)->get()->first();
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $owner->id;
        $active->user_id = Auth::user()->id;
        $active->city_id = $owner->city_id;
        $active->action_id = $id;
        $active->action_type = 'driver_exam_c';
        $active->action = "Haydovchilik imtihoni bekor qilindi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
        DB::table('driver_exams')->where('id', '=', $id)->delete();
        return redirect('/driver-exam/list')->with('message','Successfully Deleted');
    }

    public function driver_licences(Request $request){
        $settings = settings();
        if (!$settings->driver_licenses_enabled && !auth()->user()->role == 'admin') {
            return redirect('https://tm.uzagroteh.uz');
        }


        $from=$request->input('from');
        $till=$request->input('till');
        $title="Traktorchi-mashinist guvohnomalari";



        $driverLicences = DriverLicence::join('customers','customers.id','=','driver_licences.owner_id')->
        leftjoin('tbl_cities', 'tbl_cities.id', '=', 'customers.city_id')->
        leftjoin('tbl_states', 'tbl_states.id', '=', 'tbl_cities.state_id');

        $user=Auth::User();
        if($user->role != 'admin'){
            $position = DB::table('tbl_accessrights')->where('id', '=', intval($user->role))->get()->first();
            if(!empty($position)){
                if($position->position == 'district'){
                    $i = 0;
                    $driverLicences = $driverLicences->where(function($query) use($user){
                        foreach (explode(',', $user->city_id) as $city) {
                            $query->orWhere('tbl_cities.id', '=', $city);
                        }
                    });
                }elseif($position->position == 'country'){
                    $i = 0;
                    $driverLicences = $driverLicences->where(function($query) use($user){
                        foreach(explode(',', $user->state_id) as $state){
                            $query->orWhere('tbl_states.id','=',$state);
                        }
                    });
                }elseif($position->position == 'region'){
                    $i = 0;
                    $driverLicences = $driverLicences->where(function($query) use($user){
                        $cities = DB::table('tbl_cities')->where('state_id', '=', intval($user->state_id))->get()->toArray();
                        foreach ($cities as $city) {
                            $query->orWhere('tbl_cities.id', '=', $city->id);
                        }
                    });
                }
            }
        }

        if($from && $till){
            $fromTime=join('-',array_reverse(explode('-',$from)));
            $tillTime=join('-',array_reverse(explode('-',$till)));
            $timeField='driver_licences.given_date';
            $driverLicences=$driverLicences->whereDate($timeField,'>=',$fromTime)
                ->whereDate($timeField,'<=',$tillTime);
        }

        // search engine
        $s = $request->input('s');
        if($s){
            $driverLicences = $driverLicences->where(function($query) use($s){
                $columnsForSearch = [
                    DB::raw("CONCAT(UPPER(driver_licences.series), UPPER(driver_licences.number))"),
                    DB::raw("CONCAT(UPPER(customers.lastname), ' ', UPPER(customers.name), ' ', UPPER(customers.middlename))"),
                    'customers.id_number',
                    'customers.name'
                ];

                if (is_numeric($s)) {
                    $columnsForSearch[] = 'customers.inn';
                    $columnsForSearch[] = 'customers.pinfl';
                }

                foreach($columnsForSearch as $col){
                    $query = $query->orWhere($col, 'like', '%'.$s.'%');
                }
            });
        }

        $driverLicences=$driverLicences->select(
            'driver_licences.*',
            'customers.name',
            'customers.middlename',
            'customers.lastname',
            'customers.city_id',
            'customers.id_number',
            'customers.pinfl',
            'customers.inn',
            'driver_licences.type as licence_type'
        )
            //->orderBy('driver_licences.number','DESC')->get()->toArray();
            ->latest()->paginate(50);

        if($s){
            $driverLicences->appends(['s' => $s]);
        }

        $areas = Area::whereIn('id', $driverLicences->pluck('city_id'))->with('region')->get();
        $datetime1 = new DateTime(date('d-m-Y'));
        foreach ($driverLicences as $driver_lic) {
            $datetime2 = new DateTime(date('d-m-Y', strtotime($driver_lic->created_at)));
            $interval = $datetime1->diff($datetime2);
            $driver_lic->day = $interval->format('%a');
        }

        return view('driver-licence.list',compact('title','driverLicences','from','till', 'areas'));
    }

    // function to handle ajax request for cancelling technical passport, certificate or driver license
    public function cancel_given_document(Request $request){
        $document=$request->input('document'); // technical-passport || certificate || driver-license
        $id=$request->input('id');
        if($document=='technical-passport'){
            DB::table('technical_passports')->where('id', '=', $id)->delete();
        }

    }

    public function driver_exam_form(Request $request){

        $title="Haydovchi imtihonlarini topshirish";

        $examTypes=DB::table('driver_exam_types')->get()->toArray();

        $customerId=$request->input('owner_id');

        $payment_r = DB::table('tbl_payment_types')->where([['category', '=', 'driver_exam'], ['code', '=', 'rec']])->get()->toArray();
        $payment_n = DB::table('tbl_payment_types')->where([['category', '=', 'driver_exam'], ['code', '=', 'new']])->get()->toArray();
        $min = DB::table('tbl_payment_types')->where('category', '=', 'min')->get()->first();

        if($customerId){

            $customer=DB::table('customers')

                ->join('ownership_forms','ownership_forms.id','=','customers.form')

                ->where('customers.id',$customerId)

                ->select('customers.*','ownership_forms.name as ownership_form')

                ->first();

        }

        return view('driver-exam.form',compact('title','examTypes','customer', 'payment_r', 'payment_n', 'min'));
    }

    public function driver_exam_store(Request $request){
        $user = Auth::user();
        $customerId=$request->input('customer_id');
        $totalAmount=$request->input('payment');
        $paymentStatus='unpaid';
        $paymentDate=null;
        $paymentStatus='paid';
        $paymentDate=date('Y-m-d');
        $dExam=new DriverExam;
        $dExam->owner_id=$customerId;
        $dExam->type=$request->input('type');
        $dExam->result=$request->input('result');
        $dExam->total_amount=$totalAmount;
        $dExam->paid_amount=$totalAmount;
        $dExam->given_date=join('-',array_reverse(explode('-',$request->input('given_date'))));
        $dExam->payment_status=$paymentStatus;
        $dExam->payment_date=$paymentDate;
        $dExam->user_id=Auth::user()->id;
        $dExam->save();
        $last_id = DB::table('driver_exams')->orderBy('id', 'desc' )->get()->first();
        echo 'success';
        $owner = DB::table('customers')->where('id', '=', $customerId)->get()->first();
        $active = new tbl_activities;
        $active->ip_adress = $_SERVER['REMOTE_ADDR'];
        $active->owner_id = $customerId;
        $active->user_id = $user->id;
        $active->city_id = $owner->city_id;
        $active->action_id = $last_id->id;
        $active->action_type = 'driver_exam';
        $active->action = " Haydovchilik imtihonini topshirdi";
        $active->time = date('Y-m-d H:i:s');
        $active->save();
    }

    public function driver_exams(Request $request){
        return;
        $from=$request->input('from');
        $till=$request->input('till');
        $title="Haydovchi  imtihonlari";

        $driverExams=DriverExam::join('customers','customers.id','=','driver_exams.owner_id')->
        join('driver_exam_types','driver_exam_types.id','=','driver_exams.type')
            ->select(
                'customers.*',
                'driver_exams.*',
                'driver_exam_types.name as exam_type'
            )->
            leftjoin('tbl_cities', 'tbl_cities.id', '=', 'customers.city_id')->
            leftjoin('tbl_states', 'tbl_states.id', '=', 'tbl_cities.state_id');
        $user=Auth::User();
        if($user->role != 'admin'){
            $position = DB::table('tbl_accessrights')->where('id', '=', intval($user->role))->get()->first();
            if(!empty($position)){
                if($position->position == 'district'){
                    $i = 0;
                    $driverExams = $driverExams->where(function($query) use($user){
                        foreach (explode(',', $user->city_id) as $city) {
                            $query->orWhere('tbl_cities.id', '=', $city);
                        }
                    });
                }elseif($position->position == 'country'){
                    $i = 0;
                    $driverExams = $driverExams->where(function($query) use($user){
                        foreach(explode(',', $user->state_id) as $state){
                            $query->orWhere('tbl_states.id','=',$state);
                        }
                    });
                }elseif($position->position == 'region'){
                    $i = 0;
                    $driverExams = $driverExams->where(function($query) use($user){
                        $cities = DB::table('tbl_cities')->where('state_id', '=', intval($user->state_id))->get()->toArray();
                        foreach ($cities as $city) {
                            $query->orWhere('tbl_cities.id', '=', $city->id);
                        }
                    });
                }
            }
        }

        if($from && $till){
            $fromTime=join('-',array_reverse(explode('-',$from)));
            $tillTime=join('-',array_reverse(explode('-',$till)));
            $timeField='driver_exams.given_date';
            $driverExams=$driverExams->whereDate($timeField,'>=',$fromTime)
                ->whereDate($timeField,'<=',$tillTime);
        }
        $driverExams = $driverExams->orderBy('driver_exams.given_date','DESC')->
        orderBy('driver_exams.created_at','DESC')->paginate();
        $datetime1 = new DateTime(date('d-m-Y'));
        foreach ($driverExams as $driver_lic) {
            $datetime2 = new DateTime(date('d-m-Y', strtotime($driver_lic->created_at)));
            $interval = $datetime1->diff($datetime2);
            $driver_lic->day = $interval->format('%a');
        }

        return view('driver-exam.list',compact('title','driverExams','from','till'));
    }


    public function add_exam_type(Request $request){  // ajax

        $examType=$request->input('examType');



        $count = DB::table('driver_exam_types')->where('name','=',$examType)->count();



        if ($count==0){

            $cat = new DriverExamType;

            $cat -> name = $examType;

            $cat -> save();

            echo $cat->id;

        }

        if($request->input('redirect')){
            return redirect('/exam-type/list');
        }

        if($count){
            return "01";
        }
    }

    public function edit_exam_type(Request $request,$examTypeId){
        if(empty($examTypeId)){
            $examTypeId=$request->input('examTypeId');
        }

        $name=$request->input('examType');



        $examType= DriverExamType::find($examTypeId);

        $examType->name=$name;

        $examType->save();

        if($request->input('redirect')){
            return redirect('/exam-type/list');
        }
    }

    public function exam_types(){
        $title="Imtihon turlari";
        $examTypes = DB::table('driver_exam_types')->orderBy('id','DESC')->get()->toArray();

        return view('exam-type.list',compact('examTypes','title'));
    }

    public function exam_type_form($examTypeId=0){
        if($examTypeId){
            $examType=DB::table('driver_exam_types')->where('id','=',$examTypeId)->first();
            $title="Imtihon turini o'zgartirish";
        }else{
            $title='Imtihon turi qo\'shish';
        }
        return view('exam-type.add',compact('title','examType'));
    }

    public function tm_1_form(Request $request){
        if($request->input('id')){
            $tm = DB::table('tbl_tms')->where('id', '=', $request->input('id'))->get()->first();
            $vehicleId = $tm->vehicle_id;
            $ownerId = $tm->owner_id;
            $type = 'old';
            $tm_num = $tm->number;
        }else{
            $vehicleId=$request->input('vehicle_id');
            $ownerId=$request->input('owner_id');
            $type = 'new';
            $tm_num = DB::table('tbl_tms')->orderBy('id', 'desc')->get()->first();
            if(empty($tm_num)){
                $tm_num = 1;
            }else{
                $tm_num = DB::table('tbl_tms')->orderBy('id', 'desc')->get()->first()->number+1;
            }
            $tm = null;
        }

        $user=Auth::user();
        $min = DB::table('tbl_payment_types')->where('category', '=', 'min')->get()->first();
        $payment = DB::table('tbl_payment_types')->where('category', '=', 'vehicle_tm')->get()->first();

        $vehicle=DB::table('tbl_vehicles')->
        select('tbl_vehicles.*', 'tbl_vehicle_types.id as vehicletype_id')
            ->join('tbl_vehicle_brands', 'tbl_vehicle_brands.id', '=', 'tbl_vehicles.vehiclebrand_id')
            ->join('tbl_vehicle_types', 'tbl_vehicle_types.id', '=', 'tbl_vehicle_brands.vehicle_id')
            ->where('tbl_vehicles.id','=',$vehicleId)
            ->where('tbl_vehicles.owner_id','=',$ownerId)
            ->first();

        if($vehicle) {
            if($vehicle->type=='vehicle' || $vehicle->type=='tirkama'){
                $document=getActiveTechPassport($vehicle->id);
            }else{
                $document=getActiveCertificate($vehicle->id);
            }

            $vehicle->passport_number=!empty($document)?$document->number:'Berilmagan';
            $vehicle->passport_series=!empty($document)?$document->series: '';

            $vehicle->vehicle_type=getVehicleType($vehicle->vehicletype_id);
            $vehicle->brand=getVehicleBrands($vehicle->vehiclebrand_id);

            $registrationRecord=getLastRegistration($vehicle);
            $vehicle->registration_date=(!empty($registrationRecord) && $registrationRecord->action=='regged') ? date('d.m.Y',strtotime($registrationRecord->date)) : 'Ro\'yxatga olinmagan';
        }

        $lock = DB::table('vehicle_prohibitions')->where([['status', '=', 'active'],['vehicle_id', '=', $vehicleId]])->get()->first();

        if($vehicle && $lock && $vehicle->lock_status === 'lock'){
            $locker = DB::table('vehicle_lockers')->where('id', '=', $lock->locker_id)->get()->first();
            $vehicle->locker = $locker->name;
            $vehicle->lock_date = $lock->date;
        }else{
            $vehicle = (object) [];

            $vehicle->locker = null;
            $vehicle->lock_date = null;
        }

        if($vehicle->type != 'agregat'){
            $number = DB::table('transport_numbers')->where([['status', '=', 'active'], ['vehicle_id', '=', $vehicleId]])->get()->first();
            $vehicle->t_code = $number ? $number->code : null;
            $vehicle->t_series = $number ? $number->series : null;
            $vehicle->t_number = $number ? $number->number : null;
            $vehicle->t_number_type = $number ? $number->type : null;
        }else{
            $vehicle->t_code = null;
            $vehicle->t_series = null;
            $vehicle->t_number = null;
            $vehicle->t_number_type = null;
        }

        if($user->role!=='admin'){
            $user->role=DB::table('tbl_accessrights')->where('id','=',$user->role)->first()->name;
        }

        $owner=DB::table('customers')
            ->join('tbl_cities','tbl_cities.id','=','customers.city_id')
            ->join('tbl_states','tbl_states.id','=','tbl_cities.state_id')
            ->join('ownership_forms', 'ownership_forms.id', '=', 'customers.form')
            ->where('customers.id','=',$ownerId)
            ->select(
                'customers.*',
                'tbl_cities.name as city',
                'tbl_states.name as state',
                'ownership_forms.name as ownerform'
            )->first();


        $title='TM-1 ma\'lumotnoma';
        return view('vehicle.oldtm-1',compact('title','vehicle','owner','user', 'min', 'payment', 'type', 'tm', 'tm_num'));
    }

    //color delete
    public function exam_type_delete($id){

        $colors = DB::table('driver_exam_types')->where('id','=',$id)->delete();

        return redirect('/exam-type/list')->with('message','Successfully Deleted');
    }

    public function searchCustomer(Request $request){

        $type=$request->input('type');
        $search=$request->input('search');
        $driverLicenceInfo=$request->input('driver_licence_info');
        $stateInfo=$request->input('stateInfo');
        $customers = Customer::query()->join('ownership_forms','ownership_forms.id','=','customers.form');

        $user=Auth::User();
        $customers = $customers->where(function($query) use($search){
            $query->where('customers.name', 'like', '%'.$search.'%')
                ->orWhere(DB::raw("CONCAT(UPPER(customers.lastname), ' ', UPPER(customers.name), ' ', UPPER(customers.middlename))"), 'like', '%'.$search.'%')
                ->orWhere('customers.passport_number', 'like', '%'.$search.'%')->
                orWhere('customers.id_number', 'like', '%'.$search.'%');

            if (is_numeric($search)) {
                $query->orWhere('customers.pinfl', 'like', '%' . $search . '%')
                    ->orWhere('customers.inn', 'like', '%' . $search . '%');
            }
        });
        if($user->role != 'admin'){
            $position = DB::table('tbl_accessrights')->where('id', '=', intval($user->role))->get()->first();
            if(!empty($position)){
                if($position->position == 'district'){
                    $customers = $customers->where(function($query) use($user){
                        foreach (explode(',', $user->city_id) as $city) {
                            $query->where('customers.city_id', '=', intval($city))->
                            orWhere('customers.city_id', '=', intval($city))->
                            orWhere('customers.city_id', '=', intval($city))->
                            orWhere('customers.city_id', '=', intval($city))->
                            orWhere('customers.city_id', '=', intval($city));
                        }
                    });
                }elseif($position->position == 'country'){
                    $customers = $customers->where(function($query) use($user){
                        foreach(explode(',', $user->state_id) as $state){
                            $cities = DB::table('tbl_cities')->where('state_id', '=', intval($state))->get()->toArray();
                            foreach ($cities as $city) {
                                $query->where('customers.city_id', '=', intval($city->id))->
                                orWhere('customers.city_id', '=', intval($city->id))->
                                orWhere('customers.city_id', '=', intval($city->id))->
                                orWhere('customers.city_id', '=', intval($city->id))->
                                orWhere('customers.city_id', '=', intval($city->id));
                            }

                        }
                    });
                }elseif($position->position == 'region'){
                    $customers = $customers->where(function($query) use($user){
                        $cities = DB::table('tbl_cities')->where('state_id', '=', intval($user->state_id))->get()->toArray();
                        foreach ($cities as $city) {
                            $query->where('customers.city_id', '=', intval($city->id))->
                            orWhere('customers.city_id', '=', intval($city->id))->
                            orWhere('customers.city_id', '=', intval($city->id))->
                            orWhere('customers.city_id', '=', intval($city->id))->
                            orWhere('customers.city_id', '=', intval($city->id));
                        }
                    });
                }
            }
        }
        if($type){
            $customers=$customers->where('customers.type','=',$type);
        }
        $select=['customers.*','ownership_forms.name as ownership_form'];
        if($stateInfo){
            $customers=$customers->join('tbl_cities','tbl_cities.id','=','customers.city_id')
                ->join('tbl_states','tbl_states.id','=','tbl_cities.state_id');
            $select[]='tbl_states.id as state_id';
        }
        if($driverLicenceInfo){
            $customers=$customers->leftJoin('driver_licences',function($join){
                $join->on('customers.id','=','driver_licences.owner_id')
                    ->where('driver_licences.status','=','active');
            });
            $select[]='driver_licences.series as licence_series';
            $select[]='driver_licences.number as licence_number';
            $select[]='driver_licences.type as licence_type';
        }
        $customers = $customers->select($select)->take(15)->get();

        return new CustomerSearchResultResource($customers);
    }

    // get transports data for selectmenu
    public function getTransports(Request $request){
        $customer_id=$request->input('customer_id');
        $type=$request->input('type');
        $chosenVehicle=$request->input('chosen_vehicle');

        $transports=DB::table('tbl_vehicles')->
        join('tbl_vehicle_brands', 'tbl_vehicle_brands.id', '=', 'tbl_vehicles.vehiclebrand_id')->
        join('tbl_vehicle_types', 'tbl_vehicle_types.id', '=', 'tbl_vehicle_brands.vehicle_id');

        $select=['tbl_vehicles.id',
            'tbl_vehicles.type as main_type',
            'tbl_vehicles.engineno',
            'tbl_vehicles.modelyear',
            'tbl_vehicles.vehiclebrand_id',
            'tbl_vehicles.factory_number',
            'tbl_vehicle_types.id as vehicletype_id'
        ];

        if($type=='agregat'){
            $transports=$transports->leftJoin('vehicle_certificates',function($join){
                $join->on('vehicle_certificates.vehicle_id','=','tbl_vehicles.id')
                    ->where('vehicle_certificates.status','=','active');
            })->where('tbl_vehicles.type','=','agregat');
            $select[]='vehicle_certificates.series as certificate_series';
            $select[]='vehicle_certificates.number as certificate_number';
        }elseif($type=='vehicle'){
            $transports=$transports->leftJoin('technical_passports', function($join) use ($request) {
                $join->on('technical_passports.vehicle_id','=','tbl_vehicles.id')
                    ->where('technical_passports.status','=','active')
                    ->when($request->input('customer_id'), function ($query, $customerId) {
                        $query->where('technical_passports.owner_id', $customerId);
                    });
            })->where(function($q){
                $q->where('tbl_vehicles.type','=','vehicle')
                    ->orWhere('tbl_vehicles.type','=','tirkama');
            });
            $select[]='technical_passports.series as passport_series';
            $select[]='technical_passports.number as passport_number';
        }

        $transport=$transports->where('tbl_vehicles.owner_id', $customer_id);

        $transports=$transports->select($select)->get()->toArray();

        foreach($transports as $tr){
            $trNumber = getActiveTransportNumber($tr->id, $request->input('customer_id'));

            if($trNumber && $trNumber->series && $trNumber->number && $trNumber->code){
                $number=$trNumber->code.' '.$trNumber->series.$trNumber->number;
            }else{
                $number='';
            }

            ?>

            <option number="<?=$number; ?>"
                <?php if(!empty($tr->passport_number)){ ?>
                    data-doc-series="<?=$tr->passport_series;?>"
                    passport="<?=$tr->passport_series.$tr->passport_number;?>"
                <?php }elseif(!empty($tr->certificate_number)){ ?>
                    data-doc-series="<?=$tr->certificate_series;?>"
                    certificate="<?=$tr->certificate_series.$tr->certificate_number ;?>"
                <?php } ?>
                    type="<?=$tr->main_type;?>"
                    value="<?=$tr->id;?>"

                <?php if($chosenVehicle && $chosenVehicle==$tr->id){ ?>

                    selected='selected'

                <?php } ?>
            >
                <?php $lastPart=$number ? $number : ($tr->engineno ? $tr->engineno : $tr->factory_number); ?>
                <?= getVehicleType($tr->vehicletype_id); ?> (<?= getVehicleBrands($tr->vehiclebrand_id); ?>) - <?=$tr->modelyear;?> <?=$lastPart ? '('.$lastPart.')' : '';?>
            </option>

        <?php }
    }

    function instruction(){
        $title="Foydalanish qo'llanmasi";
        return view('instruction.view',compact('title'));
    }

    public function sign(){
        return view('sign');
    }

    public function import_excel_form(Request $request){
        $translit = new Transliteration();

        if (isset($_POST["import"])){
            $allowedFileType = ['application/vnd.ms-excel','text/xls','text/xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

            if(in_array($_FILES["file"]["type"],$allowedFileType)){
                $file = $request->file('file');
                $targetPath = public_path().'/uploads/excel-data/';
                $filename = $_FILES['file']['name'];
                $file->move($targetPath, $filename);

                //move_uploaded_file($_FILES['file']['tmp_name'], $targetPath);

                $Reader = new SpreadsheetReader($targetPath.$filename);

                $sheetCount = count($Reader->sheets());


                $rowsWithError=[];
                $totalRows = 0;
                $successedRows = 0;

                for($i=0;$i<1;$i++){
                    $Reader->ChangeSheet($i);

                    foreach ($Reader as $j => $row){
                        if($j==0 || $j==1){
                            if($row[count($row)-1]=="Xatolik" && $j==0){
                                $lastColumn = count($row) - 1;
                            }

                            if(!isset($lastColumn)){
                                $row[] = $j==0 ? "Xatolik" : "";
                                $lastColumn = count($row) - 1;
                            }

                            $rowsWithError[] = $row;
                            continue;
                        }

                        //print_r($Row);
                        $order = $row[0];
                        $regDate = $row[1];
                        $ownerName = $row[2];
                        $ownerForm = $row[3];
                        $passportSeries = $row[4];
                        $passportNumber = $row[5];
                        $passportGivenDate = $row[6];
                        $passportGivenBy = $row[7];
                        $ownerDob = $row[8];
                        $inn = $row[9];
                        $city = $row[10];
                        $address = $row[11];
                        $type = $row[12];
                        $brand = $row[13];
                        $producedYear = $row[14];
                        $engineNo = $row[15];
                        $chassisNo = $row[16];
                        $color = $row[17];
                        $numType = $row[18];
                        $numCode = $row[19];
                        $numSeries = $row[20];
                        $numRegNumber = $row[21]; // ro'yxatga olish soni??
                        $pSeries = $row[22];
                        $pNumber = $row[23];
                        $cerSeries = $row[24];
                        $cerNumber = $row[25];
                        $cerOrderNum = $row[26]; // rasmiy (qayd) tartib raqami ??
                        $prohibitionOrg = $row[27];
                        $prohibitionLetterNo = $row[28];
                        $prohibitionDate = $row[29];

                        // check if the row is not empty
                        if(!$ownerName){
                            continue;
                        }

                        $totalRows++;

                        // PREPARING FIELDS
                        // prepare owner name
                        if($ownerName[0]=='"' && $ownerName[strlen($ownerName)-1]=='"'){
                            $ownerName = substr($ownerName, 1, strlen($ownerName)-2);
                        }

                        // prepare registration date
                        if($regDate && count(explode('-', $regDate))==3 ) {
                            $regDateParts = explode('-', $regDate);
                            $regDate = date('Y-m-d', mktime(0, 0, 0, $regDateParts[0], $regDateParts[1], $regDateParts[2]));
                        }

                        // prepare passport given date
                        if($passportGivenDate && count(explode('-', $passportGivenDate))==3 ) {
                            $passportGivenDateParts = explode('-', $passportGivenDate);
                            $passportGivenDatedate = date('Y-m-d', mktime(0, 0, 0, $passportGivenDateParts[0], $passportGivenDateParts[1], $passportGivenDateParts[2]));
                        }

                        // prepare date of birth
                        if($ownerDob && count(explode('-', $ownerDob))==3 ) {
                            $ownerDobParts = explode('-', $ownerDob);
                            $ownerDobdate = date('Y-m-d', mktime(0, 0, 0, $ownerDobParts[0], $ownerDobParts[1], $ownerDobParts[2]));
                        }

                        // prepare prohibition date
                        if($prohibitionDate && count(explode('-', $prohibitionDate))==3 ) {
                            $prohibitionDateParts = explode('-', $prohibitionDate);
                            $prohibitionDate = date('Y-m-d', mktime(0, 0, 0, $prohibitionDateParts[0], $prohibitionDateParts[1], $prohibitionDateParts[2]));
                        }

                        // prepare vehicle type
                        if(!$engineNo || $engineNo=='-'){
                            // agregat or tirkama
                            if($type=='тиркама' || ($pSeries && $pNumber && !$cerNumber && !$cerSeries)){
                                $vehicleType = 'tirkama';
                            }else{
                                $vehicleType = 'agragat';
                            }
                        }else{
                            $vehicleType = 'vehicle';
                        }

                        // prepare number (DRB)
                        if($numCode == 1){
                            $numCode = '01';
                        }

                        // CHECKING FIELDS
                        // check inn
                        if(!$inn || strlen($inn)!==9){
                            $row[$lastColumn]="STIR da xatolik";
                            $rowsWithError[]=$row;
                            continue;
                        }

                        // checking brand
                        $vehicleBrand = DB::table('tbl_vehicle_brands')->where('tbl_vehicle_brands.vehicle_brand', '=', $brand)->first();
                        if(empty($vehicleBrand)){
                            $row[$lastColumn]="Rusum topilmadi: ".$brand;
                            $rowsWithError[]=$row;
                            continue;
                        }

                        // checking color
                        $color = $translit->Translit($color);
                        $vehicleColor = DB::table('tbl_colors')->where('tbl_colors.color', 'like', $color)->first();
                        if(empty($vehicleColor)){
                            $row[$lastColumn]="Rang topilmadi";
                            $rowsWithError[]=$row;
                            continue;
                        }

                        // checking engine no
                        if($engineNo && $engineNo!='-' && $engineNo!='ракамсиз'){
                            $hasEngineNo = true;
                            $vehicle = DB::table('tbl_vehicles')
                                ->where('tbl_vehicles.vehiclebrand_id', '=', $vehicleBrand->id)
                                ->where('engineno', '=', $engineNo)
                                ->first();
                            if(!empty($vehicle)){
                                $row[$lastColumn]="Dvigatel raqami mavjud: Texnika-ID: ".$vehicle->id;
                                $rowsWithError[]=$row;
                                continue;
                            }
                        }

                        // checking chassisno & factory no
                        if($chassisNo){
                            $hasChassisNo = true;
                            $vehicle = DB::table('tbl_vehicles')
                                ->where('tbl_vehicles.vehiclebrand_id', '=', $vehicleBrand->id)
                                ->where(function($query) use($chassisNo){
                                    $query->where('chassisno', '=', $chassisNo)->orWhere('factory_number', '=', $chassisNo);
                                })->first();

                            if(!empty($vehicle)){
                                $row[$lastColumn]="Shassi/Zavod raqami mavjud: Texnika-ID: ".$vehicle->id;
                                $rowsWithError[]=$row;
                                continue;
                            }
                        }

                        // should have either engineno or chassisno or both
                        if(!$hasEngineNo && !$hasChassisNo){
                            $row[$lastColumn]="Dvigatel & shassi raqami topilmadi";
                            $rowsWithError[] = $row;
                            continue;
                        }

                        // checking ownership form
                        $ownerForm = $translit->Translit($ownerForm);
                        $ownershipForm = DB::table('ownership_forms')->where('ownership_forms.name', 'like', $ownerForm)->first();
                        if(empty($ownershipForm)){
                            $row[$lastColumn]="Mulkchilik shakli topilmadi";
                            $rowsWithError[]=$row;
                            continue;
                        }

                        // checking city
                        $city = $translit->Translit($city);
                        $ownerCity = DB::table('tbl_cities')->where('tbl_cities.name', '=', $city)->first();
                        if(empty($ownerCity)){
                            $row[$lastColumn]="Shahar/tuman topilmadi: ".$city;
                            $rowsWithError[]=$row;
                            continue;
                        }

                        // checking passport given city
                        $passportGivenBy = $translit->Translit($passportGivenBy);
                        $ownerPassportCity = DB::table('tbl_cities')->where('tbl_cities.name', '=', $passportGivenBy)->first();
                        if(empty($ownerPassportCity)){
                            $row[$lastColumn]="Pasport berilgan shahar/tuman topilmadi: ".$passportGivenBy;
                            $rowsWithError[]=$row;
                            continue;
                        }

                        // checking number (DRB)
                        $trNumber = DB::table('transport_numbers')
                            ->leftJoin('tbl_vehicles', 'tbl_vehicles.id', '=', 'transport_numbers.vehicle_id')
                            ->where('tbl_vehicles.type', '=', $vehicleType)
                            ->where('transport_numbers.code', '=', $numCode)
                            ->where('transport_numbers.number', '=', $numType)
                            ->where('transport_numbers.series', '=', $numSeries)
                            ->where('transport_numbers.status', '=', 'active')
                            ->first();
                        if(!empty($trNumber)){
                            $row[$lastColumn] = "DRB bazada mavjud; Texnika ID: ".$trNumber->vehicle_id;
                            $rowsWithError[]=$row;
                            continue;
                        }

                        // checking technical passport
                        $techPassport = DB::table('technical_passports')
                            ->where('technical_passports.status', '=', 'active')
                            ->where('technical_passports.series', '=', $pSeries)
                            ->where('technical_passports.number', '=', $pNumber)
                            ->first();
                        if(!empty($techPassport)){
                            $row[$lastColumn] = "Texnik pasport bazada mavjud (".$pSeries.$pNumber."); Texnika ID: ".$techPassport->vehicle_id;
                            $rowsWithError[] = $row;
                            continue;
                        }

                        // checking prohibition organization
                        if($prohibitionOrg){
                            $prohibitionOrg = $translit->Translit($prohibitionOrg);
                            $lockerOrg = DB::table('vehicle_lockers')->where('vehicle.name', '=', $prohibitionOrg)->first();
                            if(empty($ownerCity)){
                                $row[$lastColumn]="Taqiqqa oluvchi tashkilot topilmadi: ".$prohibitionOrg;
                                $rowsWithError[]=$row;
                                continue;
                            }
                        }

                        // CUSTOMER PART
                        $customer = Customer::where('inn', '=', trim($inn))->first();
                        if(empty($customer)){
                            // customer not found, we create a new one
                            $customer = new Customer;

                            // type
                            if($passportSeries && $passportNumber){
                                $customerType='physical';

                                // passport details
                                $customer->passport_series = $passportSeries;
                                $customer->passport_number = $passportNumber;
                                $customer->passport_given_date = $passportGivenDate;
                                $customer->passport_given_city = $ownerPassportCity->id;
                                $customer->d_o_birth = $ownerDob;
                            }else{
                                $customerType='legal';
                                $customer->form = $ownershipForm->id;
                            }
                            $customer->type = $customerType;

                            // name
                            if($customerType=='legal'){
                                $customer->name = $ownerName;
                            }elseif($customerType=='physical'){
                                // separate name to lastname and middlename

                            }

                            // city
                            $customer->city_id = $ownerCity->id;

                            // address
                            $customer->address = $address;

                            $customer->inn = $inn;
                            $customer->residence = 1;
                            $customer->status = 1;

                            $newCustomerSaved = $customer->save();
                        }else{
                            // customer exists, we check for other details
                            if($customer->name != $ownerName){
                                $row[$lastColumn]="Texnika egasi nomi to'g'ri kelmadi: Excelda: ".$ownerName."; Tizimda: ".$customer->name;
                                $rowsWithError[]=$row;
                                continue;
                            }
                        }

                        // VEHICLE PART
                        $vehicle = new tbl_vehicles;
                        $vehicle->vehicletype_id = $vehicleBrand->vehicle_id;
                        $vehicle->chassisno = $chassisNo;
                        $vehicle->vehiclebrand_id = $vehicleBrand->id;
                        $vehicle->modelyear = $producedYear;
                        $vehicle->fuel_id = 1; // not ready yet
                        $vehicle->engineno = $engineNo;
                        $vehicle->enginesize = $vehicleBrand->enginesize;
                        $vehicle->working_for_id = $vehicleBrand->working_type_id;
                        $vehicle->owner_id = $customer->id;
                        $vehicle->factory_id = $vehicleBrand->factory_id; // not ready yet,  should get from brand data
                        $vehicle->condition = 'fit';
                        $vehicle->factory_number = $chassisNo;
                        $vehicle->status = 'regged';
                        $vehicle->type = $vehicleType;
                        $vehicle->color_id = $vehicleColor->id;
                        $vehicle->lising = 0;
                        $vehicle->supplier_id = 0;
                        $vehicle->weight = $vehicleBrand->weight; // not ready yet
                        $vehicle->weight_full = $vehicleBrand->weight_full; // not ready yet

                        // lock status
                        if($prohibitionOrg && $prohibitionLetterNo && $prohibitionDate){
                            $vehicle->lock_status = 'lock';
                        }else{
                            $vehicle->lock_status = 'unlock';
                        }

                        $newVehicleSaved = $vehicle->save();

                        // REGISTRATION PART
                        $registration = new vehicle_registrations;
                        $registration->owner_id = $customer->id;
                        $registration->vehicle_id = $vehicle->id;
                        $registration->status = 'active';
                        $registration->action = 'regged';
                        $registration->doc = 10; // ASOS: mavjud bazadan elektron bazaga o'tkazish
                        $registration->doc_note = 'Excel import';
                        $registration->date = $regDate;
                        $registration->total_amount = 0;
                        $registration->paid_amount = 0;
                        $registration->note = 'excel import';
                        $registration->city_id = $customer->city_id;
                        $registration->user_id = 0;
                        $registration->outof = 0;
                        $registration->unfit = 0;

                        $registrationSaved = $registration->save();

                        // NUMBER AND TECHNICAL PASSPORT PART (DRB)
                        if($vehicleType=='vehicle' || $vehicleType=='tirkama'){
                            $trNumber = new TransportNumber;
                            $trNumber->owner_id = $customer->id;
                            $trNumber->vehicle_id = $vehicle->id;
                            $trNumber->series = $numSeries;
                            $trNumber->code = $numCode;
                            $trNumber->number = $numType;
                            $trNumber->given_date = $regDate;
                            $trNumber->action = 'give';
                            $trNumber->doc = 12; // ASOS: mavjud davlat raqamini elektron bazaga o'tkazish
                            $trNumber->doc_note = 'excel import';
                            $trNumber->status = 'active';
                            $trNumber->payment_status = 'paid';
                            $trNumber->total_amount = 0;
                            $trNumber->state_id = $ownerCity->state_id;
                            if($customer->type == 'legal'){
                                if($vehicleType == 'vehicle'){
                                    $trNumber->type = 1; // yuridik o'ziyurar
                                }else{
                                    $trNumber->type = 3; // yuridik tirkama
                                }
                            }else{
                                if($vehicleType == 'vehicle'){
                                    $trNumber->type = 2; // jismoniy o'ziyurar
                                }else{
                                    $trNumber->type = 4; // jismoniy tirkama
                                }
                            }
                            $trNumber->user_id = 0;
                            $trNumberSaved = $trNumber->save();

                            // TECHNICAL PASSPORT PART
                            $techPassport = new TechnicalPassport;
                            $techPassport->owner_id = $customer->id;
                            $techPassport->vehicle_id = $vehicle->id;
                            $techPassport->series = $pSeries;
                            $techPassport->number = $pNumber;
                            $techPassport->action = 'give';
                            $techPassport->doc = 19; // 	ASOS: mavjud tex pasportni elektron bazaga kiritish
                            $techPassport->doc_note = 'excel import';
                            $techPassport->status = 'active';
                            $techPassport->payment_status = 'paid';
                            $techPassport->total_amount = 0;
                            $techPassport->user_id = 0;
                            $techPassport->given_date = $regDate;

                            $techPassportSaved = $techPassport->save();
                        }else{
                            // CERTIFICATE PART
                            $certificate = new vehicle_certificates;
                            $certificate->owner_id = $customer->id;
                            $certificate->vehicle_id = $vehicle->id;
                            $certificate->series = $cerSeries;
                            $certificate->number = $cerNumber;
                            $certificate->action = 'give';
                            $certificate->doc = 14; // ASOS: mavjud guvohnomani yangi ko'rinishga
                            $certificate->doc_note = 'excel import';
                            $certificate->given_date = $regDate;
                            $certificate->status = 'active';
                            $certificate->user_id = 0;
                            $certificate->total_amount = 0;
                            $certificate->payment_status = 'paid';

                            $certificateSaved = $certificate->save();
                        }

                        // PROHIBITION PART
                        if($vehicle->lock_status == 'lock'){
                            $prohibition = new vehicle_prohibitions;
                            $prohibition->owner_id = $customer->id;
                            $prohibition->vehicle_id = $vehicle->id;
                            $prohibition->locker_id = $lockerOrg->id;
                            $prohibition->user_id = 0;
                            $prohibition->date = $prohibitionDate;
                            $prohibition->action = 'lock';
                            $prohibition->status = 'active';
                            $prohibition->letter_number = $prohibitionLetterNo;
                            $prohibition->letter_date = $prohibitionDate;

                            $prohibitionSaved = $prohibition->save();
                        }
                        $successedRows++;
                    }
                }

                return view('excel.upload', compact('rowsWithError', 'successedRows', 'totalRows'));

            }else{
                $type = "error";
                $message = "Invalid File Type. Upload Excel File.";
            }
        }else{
            return view('excel.upload');
        }
    }

}

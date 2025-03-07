<?php

use App\Models\Application;
use App\Models\CropsName;
use App\Models\DefaultModels\tbl_settings;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

const USER_DATE_FORMAT = 'd-m-Y';
const USER_DOT_DATE_FORMAT = 'd.m.Y';
const USER_DATE_TIME_FORMAT = 'H:i:s d-m-Y';

const NAVOI_ID = 4008;
const ANDIJON_ID = 3999;
const BUXORO_ID = 4000;
const JIZZAX_ID = 4002;
const TASHKENT_ID = 4121;
const SAMARQAND_ID = 4009;
const NAMANGAN_ID = 4007;
const QASHQADARYO_ID = 4005;
const SURXONDARYO_ID = 4011;
const SIRDARYO_ID = 4010;
const TOSHVIL_ID = 4012;

const XORAZM_ID = 4004;
const KARAKALPAKSTAN_ID = 4006;
const FARGONA_ID = 4003;

if (!function_exists('formatUzbekDate')) {
    function formatUzbekDate($date)
    {
        $uzbekMonthNames = [
            '01' => 'yanvar',
            '02' => 'fevral',
            '03' => 'mart',
            '04' => 'aprel',
            '05' => 'may',
            '06' => 'iyun',
            '07' => 'iyul',
            '08' => 'avgust',
            '09' => 'sentabr',
            '10' => 'oktabr',
            '11' => 'noyabr',
            '12' => 'dekabr'
        ];

        $date = Carbon::parse($date);
        return $date->isoFormat("D") . ' - ' . $uzbekMonthNames[$date->isoFormat("MM")] . ' ' . $date->isoFormat("Y");
    }
}
if (!function_exists('formatUzbekDateInLatin')) {
    function formatUzbekDateInLatin($date)
    {
        $formattedDate = \Carbon\Carbon::parse($date)
            ->setTimezone('Asia/Tashkent') // Set your desired timezone (e.g., Tashkent for Uzbekistan)
            ->locale('uz')
            ->translatedFormat('j F, Y');

    // Replace Cyrillic month and period names with Latin equivalents
        $formattedDate = str_replace(
            ['январ', 'феврал', 'март', 'апрел', 'май', 'июн', 'июл', 'август', 'сентябр', 'октябр', 'ноябр', 'декабр', 'эрталаб', 'тушдан кейин'],
            ['yanvar', 'fevral', 'mart', 'aprel', 'may', 'iyun', 'iyul', 'avgust', 'sentabr', 'oktabr', 'noyabr', 'dekabr', 'ertalab', 'tushdan keyin'],
            $formattedDate
        );

       return $formattedDate;
    }
}

if (!function_exists('getAccessStatusUser')) {
    function getAccessStatusUser($menu_name, $id)
    {
        return 'yes';
    }
}

// Get active Admin list in data list

if (!function_exists('getActiveAdmin')) {

    function getActiveAdmin($id)
    {
        return 'yes';
    }

}

// Get active Customer list in data list

if (!function_exists('getActiveCustomer')) {
    function getActiveCustomer()
    {
        return 'yes';
    }
}


// Get active Employee list in data list

if (!function_exists('getActiveEmployee')) {

    function getActiveEmployee()
    {
        return 'yes';
    }

}

if (!function_exists('getDateFormat')) {
    function getDateFormat()
    {
        return 'd-m-Y';
    }
}


// Get date format in datepicker

if (!function_exists('getDatepicker')) {

    function getDatepicker()
    {
        $dateformat = DB::table('tbl_settings')->first();

        $dateformate = $dateformat->date_format;

        if (!empty($dateformate)) {

            if ($dateformate == 'm-d-Y') {

                $dateformats = "mm-dd-yyyy";

                return $dateformats;

            } elseif ($dateformate == 'Y-m-d') {

                $dateformats = "yyyy-mm-dd";

                return $dateformats;

            } elseif ($dateformate == 'd-m-Y') {

                $dateformats = "dd-mm-yyyy";

                return $dateformats;

            } elseif ($dateformate == 'M-d-Y') {

                $dateformats = "MM-dd-yyyy";

                return $dateformats;

            }


        }

    }

}

function numberFormat($number, $format = '', $precision = 2)
{
    if ($format == 'million') {
        return round($number / 1000000, $precision);
    }
}

// Check access has no/yes

if (!function_exists('CheckAccessUser')) {
    function CheckAccessUser($key, $action): bool
    {
        if ($user = auth()->user()) {
            if ($user->isAdmin()) {
                return true;
            }

            $permission = \App\Services\PermissionService::getPermission($user->role, $key);
            return $permission && (bool)($permission->{$action});
        }

        return false;
    }
}

if (!function_exists('CheckAdmin')) {
    function CheckAdmin()
    {
        return optional(auth()->user())->role === 'admin' ? 'yes' : 'no';
    }
}
if (!function_exists('CheckInspektor')) {
    function CheckInspektor()
    {
        return optional(auth()->user())->role == 54 ? 'yes' : 'no';
    }
}


function settings()
{
    static $val = null;

    if ($val === null) {
        $val = \Illuminate\Support\Facades\Cache::remember('settings', 60 * 60, function () {
            return tbl_settings::first();
        });
    }

    return $val;
}
if(!function_exists('formatDate')){
    function formatDate($date)
    {
        return $date ? date('Y-m-d', strtotime($date)) : null;
    }
}

if (! function_exists('getAppStatus')) {
    function getAppStatus()
    {
        return \Illuminate\Support\Facades\Cache::remember('applications', 60*60, function () {
            return \App\Models\Application::getStatus();
        });
    }
}

if (! function_exists('getCropsNames')) {
    function getCropsNames()
    {
        return \App\Models\CropsName::get();

    }
}
if (! function_exists('getSelections')) {
    function getSelections()
    {
        return \App\Models\CropsSelection::all();

    }
}
if (! function_exists('getCountries')) {
    function getCountries()
    {
        return \Illuminate\Support\Facades\Cache::remember('countries', 60*60, function () {
            return DB::table('tbl_countries')->get()->toArray();
        });
    }
}

if (! function_exists('getRegions')) {
    function getRegions()
    {
        return \Illuminate\Support\Facades\Cache::remember('regions', 60*60, function () {
            return \App\Models\Region::all();
        });
    }
}

if (! function_exists('getCropYears')) {
    function getCropYears()
    {
        return \Illuminate\Support\Facades\Cache::remember('crop_data_years', 60*60, function () {
            return \App\Models\CropData::getYear();
        });
    }
}
if (! function_exists('getCropYears')) {
    function getCropYears()
    {
        return \Illuminate\Support\Facades\Cache::remember('crop_data_years', 60*60, function () {
            return \App\Models\CropData::getYear();
        });
    }
}
if (!function_exists('getCurrentYear')) {
    function getCurrentYear()
    {
        return session('year', 2024);
    }
}
if (!function_exists('getApplicationType')) {
    function getApplicationType()
    {
        return session('crop', 1);
    }
}
if (!function_exists('isSifatSertificate')) {
    function isSifatSertificate()
    {
        if (session('crop') == CropsName::CROP_TYPE_3 || session('crop') == CropsName::CROP_TYPE_4) {
            return true;
        }
        return false;
    }
}

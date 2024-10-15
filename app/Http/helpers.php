<?php

use App\Models\Application;
use App\Models\DefaultModels\tbl_settings;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use Carbon\Carbon;

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


const INVOICE_ENABLED_REGIONS = [
    NAVOI_ID,
    ANDIJON_ID,
    BUXORO_ID,
    JIZZAX_ID,
    TASHKENT_ID,
    SAMARQAND_ID,
    NAMANGAN_ID,
    QASHQADARYO_ID,
    SURXONDARYO_ID,
    SIRDARYO_ID,
    TOSHVIL_ID,
    XORAZM_ID,
    KARAKALPAKSTAN_ID,
    FARGONA_ID,

];
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


if (!function_exists('isSmsablePhone')) {
    function isSmsablePhone($val): bool
    {
        return strlen((string) $val) === 12 && \Illuminate\Support\Str::startsWith($val, '998');
    }
}

if (!function_exists('cleanPhone')) {
    function cleanPhone($val): string
    {
        return str_pad(preg_replace("/[^0-9]/", '', (string) $val), 12, '998', STR_PAD_LEFT);
    }
}

if (!function_exists('prettyPhone')) {
    function prettyPhone($val): string
    {
        return preg_replace('~(\d{3})(\d{2})(\d{3})(\d{2})(\d{2})~', '+$1 ($2) $3-$4-$5', cleanPhone($val));
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
if (!function_exists('CheckPosition')) {

    function CheckPosition($id)
    {
        $user = DB::table('users')->where('id', '=', $id)->get()->first();
        if (!empty($user)) {
            if ($user->role == 'admin') {
                return "admin";
            } else {
                $position = DB::table('tbl_accessrights')->where('id', '=', intval($user->role))->get()->first();
                return $position->name;
            }
        } else {
            return 'no';
        }

    }

}
if (!function_exists('getPosition')) {
    function getPosition()
    {
        $user = auth()->user();
        if (!empty($user)) {
            if ($user->role == 'admin') {
                return "admin";
            } else {
                return $user->level->position;
            }
        } else {
            return 'no';
        }
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

function prettyInt($number) {
    return number_format($number, 0, '.', ' ');
}

const MIB_BRANCHES = [
    100 => 'Марказий бошкарув',
    1000 => 'Тошкент шаҳри',
    1001 => 'Бектемир тумани',
    1002 => 'М. Улуғбек тумани',
    1003 => 'Миробод тумани',
    1004 => 'Олмазор тумани',
    1005 => 'Сергели тумани',
    1006 => 'Учтепа тумани',
    1007 => 'Чилонзор тумани',
    1008 => 'Шайхонтоҳур тумани',
    1009 => 'Юнусобод тумани',
    1010 => 'Яккасарой тумани',
    1011 => 'Яшнабод тумани',
    1100 => 'Тошкент вилояти',
    1101 => 'Ангрен шаҳар',
    1102 => 'Бекобод тумани',
    1103 => 'Бекобод шаҳар',
    1104 => 'Бўка тумани',
    1105 => 'Бўстонлиқ тумани',
    1106 => 'Зангиота тумани',
    1107 => 'Олмалиқ шаҳар',
    1108 => 'Оққўрғон тумани',
    1109 => 'Оҳангарон тумани',
    1110 => 'Паркент тумани',
    1111 => 'Пискент тумани',
    1112 => 'Чиноз тумани',
    1113 => 'Чирчиқ шаҳар',
    1114 => 'Юқоричирчиқ тумани',
    1115 => 'Янгийўл тумани',
    1116 => 'Ўртачирчиқ тумани',
    1117 => 'Қибрай тумани',
    1118 => 'Қуйичирчиқ тумани',
    1119 => 'Оҳангарон шахар',
    1120 => 'Тошкент тумани',
    1200 => 'Сирдарё вилояти',
    1201 => 'Гулистон шаҳар',
    1202 => 'Боёвут тумани',
    1203 => 'Гулистон тумани',
    1204 => 'Мирзаобод тумани',
    1205 => 'Оқолтин тумани',
    1206 => 'Сайҳунобод тумани',
    1207 => 'Сардоба тумани',
    1208 => 'Сирдарё тумани',
    1209 => 'Ховос тумани',
    1210 => 'Ширин шаҳар',
    1211 => 'Янгиер шаҳар',
    1300 => 'Жиззах вилояти',
    1301 => 'Жиззах шаҳар',
    1302 => 'Арнасой тумани',
    1303 => 'Бахмал тумани',
    1304 => 'Дустлик тумани',
    1305 => 'Ш.Рашидов тумани',
    1306 => 'Зарбдор тумани',
    1307 => 'Зафаробод тумани',
    1308 => 'Зомин тумани',
    1309 => 'Мирзачўл тумани',
    1310 => 'Пахтакор тумани',
    1311 => 'Фориш тумани',
    1312 => 'Янгиобод тумани',
    1313 => 'Галлаорол тумани',
    1400 => 'Самарқанд вилояти',
    1401 => 'Самарқанд шаҳар',
    1402 => 'Булунғур тумани',
    1403 => 'Жомбой тумани',
    1404 => 'Иштихон туман',
    1405 => 'Каттақўрғон тумани',
    1406 => 'Каттақўрғон шаҳар',
    1407 => 'Нарпай тумани',
    1408 => 'Нуробод тумани',
    1409 => 'Оқдарё тумани',
    1410 => 'Пайариқ тумани',
    1411 => 'Пастдарғом тумани',
    1412 => 'Пахтачи тумани',
    1413 => 'Самарқанд тумани',
    1414 => 'Тайлоқ тумани',
    1415 => 'Ургут тумани',
    1416 => 'Қўшрабод тумани',
    1500 => 'Фарғона вилояти',
    1501 => 'Фарғона шаҳар',
    1502 => 'Боғдод тумани',
    1503 => 'Бешариқ тумани',
    1504 => 'Бувайда тумани',
    1505 => 'Данғара тумани',
    1506 => 'Ёзъёвон тумани',
    1507 => 'Марғилон шаҳар',
    1508 => 'Олтиариқ тумани',
    1509 => 'Риштон тумани',
    1510 => 'Сўх тумани',
    1511 => 'Тошлоқ тумани',
    1512 => 'Учкўприк тумани',
    1513 => 'Фарғона тумани',
    1514 => 'Фурқат тумани',
    1515 => 'Ўзбекистон тумани',
    1516 => 'Қува тумани',
    1517 => 'Қувасой шаҳар',
    1518 => 'Кўштепа тумани',
    1519 => 'Қўқон шаҳар',
    1600 => 'Наманган вилояти',
    1601 => 'Наманган шахри',
    1602 => 'Косонсой тумани',
    1603 => 'Мингбулоқ тумани',
    1604 => 'Наманган тумани',
    1605 => 'Норин тумани',
    1606 => 'Поп тумани',
    1607 => 'Тўрақўрғон тумани',
    1608 => 'Уйчи тумани',
    1609 => 'Учқўрғон тумани',
    1610 => 'Чортоқ тумани',
    1611 => 'Чуст тумани',
    1612 => 'Янгиқўрғон тумани',
    1700 => 'Андижон вилояти',
    1701 => 'Андижон шаҳри',
    1702 => 'Андижон тумани',
    1703 => 'Асака тумани',
    1704 => 'Балиқчи тумани',
    1705 => 'Булоқбоши  тумани',
    1706 => 'Бўз тумани',
    1707 => 'Жалақудуқ  туман',
    1708 => 'Избоскан тумани',
    1709 => 'Мархамат тумани',
    1710 => 'Олтинкўл  туман',
    1711 => 'Пахтаобод тумани',
    1712 => 'Улуғнор туман',
    1713 => 'Хонобод шаҳри',
    1714 => 'Хўжаобод тумани',
    1715 => 'Шаҳрихон тумани',
    1716 => 'Қўрғонтепа  туман',
    1800 => 'Қашқадарё вилояти',
    1801 => 'Қарши шаҳри',
    1802 => 'Деҳқонобод тумани',
    1803 => 'Касби тумани',
    1804 => 'Китоб тумани',
    1805 => 'Косон тумани',
    1806 => 'Миришкор тумани',
    1807 => 'Муборак тумани',
    1808 => 'Нишон тумани',
    1809 => 'Чироқчи тумани',
    1810 => 'Шаҳрисабз тумани',
    1811 => 'Яккабоғ тумани',
    1812 => 'Қамаши тумани',
    1813 => 'Қарши тумани',
    1814 => 'Ғузор тумани',
    1815 => 'Шаҳрисабз шаҳри',
    1900 => 'Сурхондарё вилояти',
    1901 => 'Термиз шаҳар',
    1902 => 'Ангор тумани',
    1903 => 'Бойсун тумани',
    1904 => 'Денов тумани',
    1905 => 'Жарқўрғон тумани',
    1906 => 'Музработ тумани',
    1907 => 'Олтинсой тумани',
    1908 => 'Сариосиё тумани',
    1909 => 'Термиз тумани',
    1910 => 'Узун тумани',
    1911 => 'Шеробод тумани',
    1912 => 'Шўрчи тумани',
    1913 => 'Қизириқ тумани',
    1914 => 'Қумқўрғон тумани',
    1915 => 'Денов шаҳар',
    2000 => 'Бухоро вилояти',
    2001 => 'Бухоро шаҳри',
    2002 => 'Бухоро тумани',
    2003 => 'Вобкент тумани',
    2004 => 'Жондор тумани',
    2005 => 'Когон тумани',
    2006 => 'Когон шаҳри',
    2007 => 'Олот тумани',
    2008 => 'Пешку тумани',
    2009 => 'Ромитан тумани',
    2010 => 'Шофиркон тумани',
    2011 => 'Қоракўл тумани',
    2012 => 'Қоровулбозор тумани',
    2013 => 'Ғиждувон тумани',
    2100 => 'Навоий вилояти',
    2101 => 'Навоий шаҳар',
    2102 => 'Зарафшон шаҳар',
    2103 => 'Кармана тумани',
    2104 => 'Конимех тумани',
    2105 => 'Навбаҳор тумани',
    2106 => 'Нурота тумани',
    2107 => 'Томди тумани',
    2108 => 'Учқудуқ тумани',
    2109 => 'Хатирчи тумани',
    2110 => 'Қизилтепа тумани',
    2200 => 'Хоразм вилояти',
    2201 => 'Урганч шаҳар',
    2202 => 'Боғот туман',
    2203 => 'Гурлан тумани',
    2204 => 'Урганч тумани',
    2205 => 'Хазорасп тумани',
    2206 => 'Хива тумани',
    2207 => 'Хонқа тумани',
    2208 => 'Шовот тумани',
    2209 => 'Янгиариқ тумани',
    2210 => 'Янгибозор тумани',
    2211 => 'Қўшкўпир тумани',
    2300 => 'Қорақалпоғистон Республикаси',
    2301 => 'Нукус шаҳар',
    2302 => 'Амударё тумани',
    2303 => 'Беруний тумани',
    2304 => 'Кегейли тумани',
    2305 => 'Муйноқ тумани',
    2306 => 'Нукус тумани',
    2307 => 'Тахтакўпир тумани',
    2308 => 'Тўрткўл тумани',
    2309 => 'Хўжайли тумани',
    2310 => 'Чимбой тумани',
    2311 => 'Шуманой тумани',
    2312 => 'Элликқалъа тумани',
    2313 => 'Қонликўл тумани',
    2314 => 'Қораўзак тумани',
    2315 => 'Қўнғирот тумани',
    2316 => 'Тахиатош тумани',
    2317 => 'Кегейли тумани – Халқобод',
    2318 => 'Питнак шаҳар',
    2319 => 'Даштобод шаҳарчаси',
    2321 => 'Нурафшон шахар',
    2323 => 'Хива шаҳар',
    2324 => 'Янгийул шахар',
    2325 => 'Кегейли тумани – Бозатов',
    2326 => 'Бандихон тумани',
];

function getJWTPayload($token) {
    $els = explode('.', $token);
    $base64 = str_replace(['-', '+'], ['_', '/'], $els[1]);
    return json_decode(base64_decode($base64));
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
        return \Illuminate\Support\Facades\Cache::remember('crops_names', 60*60, function () {
            return \App\Models\CropsName::all();
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

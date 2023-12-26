<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request)
    {
        $language = $request->input('language');

        session(['language'=>$language]);
        return response()->json(['success' => true]);
    }
}

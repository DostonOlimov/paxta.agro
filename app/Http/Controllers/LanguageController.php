<?php
namespace App\Http\Controllers;


use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request)
    {
        $language = $request->input('language');
        session(['locale' => $language]); // Set the new language in the session
        \Log::info('Language set to: ' . $language);
        \Log::info('Current session: ' . print_r(session()->all(), true));
        return response()->json(['success' => true]);
    }
}

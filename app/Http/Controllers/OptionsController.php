<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Option;

class OptionsController extends Controller
{
    public function index() {
        $options = Option::firstOrFail();
        return view('manage_wikifarm', compact('options'));
    }

    public function update(Request $request) {
        //dd($request);
        $data = request()->validate([
            'protection_level' => 'string',
            'is_comments_enabled' => 'boolean',
            'is_registration_enabled' => 'boolean',
        ]);
        dd($data);
        return 'Заглушка страницы обновления параметров викифермы';
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Option;

class OptionsController extends Controller
{
    public function index() {
        $options = Option::getOptions();
        return view('manage_wikifarm', compact('options'));
    }

    public function update(Request $request) {
        $data = request()->validate([
            'protection_level' => 'required|string|in:public,comments_only,semi_public,private',
            'is_comments_enabled' => 'sometimes|accepted',
            'is_registration_enabled' => 'sometimes|accepted',
        ]);
        
        // Обрабатываем чекбоксы: если поле отсутствует, значит оно false
        $data['is_comments_enabled'] = $request->has('is_comments_enabled');
        $data['is_registration_enabled'] = $request->has('is_registration_enabled');

        $options = Option::getOptions();

        $options->update([
            'protection_level' => $data['protection_level'],
            'is_comments_enabled' => $data['is_comments_enabled'],
            'is_registration_enabled' => $data['is_registration_enabled'],
        ]);
        return 'Настройки вики-фермы успешно обновлены';
    }
}

<?php

namespace App\Http\Controllers;
use App\Models\Wiki;

use Illuminate\Contracts\View\View;
class WikisController extends Controller
{
    //Заглавная вики-фермы. Показывает все вики.
    //Аналог Special:NewWikis
    public function index()
    {
        $wikis = Wiki::all();

        return view('show-all-wikis', compact('wikis'));
    }

    //Показывает закрытые вики
    //Предназначена для участников со специальными глобальными правами
    //по умолчанию - steward
    public function trash() {
        $wikis = Wiki::onlyTrashed()->get();

        return view('show-all-closed-wikis', compact('wikis'));
    }

    //Форма создания вики. Страница с ограниченным доступом
    public function create() {
        return view('create-wiki');
    }

    //POST-ручка для формы создания вики
    public function store() {
        $data = request()->validate([
            'url' => 'string',
        ]);
        Wiki::create($data);

        return response(__('Wiki was created'), 201)
            ->header('Content-Type', 'text/plain');
    }

    //DELETE-ручка закрытия вики
    //Ограничена для пользователей без глобальных прав
    //по умолчанию - steward
    public function destroy(Wiki $wiki) {
        $wiki->delete();
        return response(__('Wiki was closed'), 200)
            ->header('Content-Type', 'text/plain');
    }

    //POST-ручка открытия вики
    //Ограничена для пользователей без глобальных прав
    //по умолчанию - steward
    public function restore($wikiId) {
        $wiki = Wiki::onlyTrashed()->findOrFail($wikiId);
        $wiki->restore();
        return response(__('Wiki was opened'), 200)
            ->header('Content-Type', 'text/plain');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wiki;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\UserUserGroupWiki;
use Illuminate\Support\Facades\DB;

class WikisController extends Controller
{
    //заглавная вики-фермы. Показывает все вики.
    //Аналог Special:NewWikis
    public function index() {
        $wikis = Wiki::all();

        return view('show-all-wikis', compact('wikis'));
    }

    //Показывает закрыте вики
    //Предназначена для участников со специальными глобальными правами
    //по умолчанию - steward
    public function show_closed_wikis() {
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

        return __('Wiki was created');
    }

    //DELETE-ручка закрытия вики
    //Ограничена для пользователь без глобльных прав
    //по умолчнию - steward
    public function destroy(Wiki $wiki) {
        $wiki->delete();
        return __('Wiki was closed');
    }

    //POST-ручка открытия вики
    //Ограничена для пользователь без глобльных прав
    //по умолчнию - steward
    public function open($wikiId) {
        $wiki = Wiki::onlyTrashed()->findOrFail($wikiId);
        $wiki->restore();
        return __('Wiki was opened');
    }
}

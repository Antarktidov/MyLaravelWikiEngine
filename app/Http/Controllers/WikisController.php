<?php

namespace App\Http\Controllers;
use App\Models\Wiki;

use Illuminate\Contracts\View\View;
class WikisController extends Controller
{
    //Заглавная вики-фермы. Показывает все вики.
    //Аналог Special:NewWikis
    public function index(): View
    {
        $wikis = Wiki::all();

        return view('show-all-wikis', compact('wikis'));
    }

    //Показывает закрытые вики
    //Предназначена для участников со специальными глобальными правами
    //по умолчанию - steward
    public function trash(): View {
        $wikis = Wiki::onlyTrashed()->get();

        return view('show-all-closed-wikis', compact('wikis'));
    }

    //Форма создания вики. Страница с ограниченным доступом
    public function create(): View {
        return view('create-wiki');
    }

    //POST-ручка для формы создания вики
    public function store(): string {
        $data = request()->validate([
            'url' => 'string',
        ]);
        Wiki::create($data);

        return __('Wiki was created');
    }

    //DELETE-ручка закрытия вики
    //Ограничена для пользователей без глобальных прав
    //по умолчанию - steward
    public function destroy(Wiki $wiki): string {
        $wiki->delete();
        return __('Wiki was closed');
    }

    //POST-ручка открытия вики
    //Ограничена для пользователей без глобальных прав
    //по умолчанию - steward
    public function restore($wikiId): string {
        $wiki = Wiki::onlyTrashed()->findOrFail($wikiId);
        $wiki->restore();
        return __('Wiki was opened');
    }
}

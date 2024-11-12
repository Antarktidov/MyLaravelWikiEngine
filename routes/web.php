<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WikisController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\RevisionController;

use App\Http\Middleware\DeleteMiddleware;
use App\Http\Middleware\DeleteRevisionMiddleware;
use App\Http\Middleware\RestoreRevisionMiddleware;
use App\Http\Middleware\ViewDeletedMiddleware;
use App\Http\Middleware\ViewDeletedRevisionsMiddleware;
use App\Http\Middleware\RestoreMiddleware;

use App\Http\Middleware\CreateWikisMiddleware;
use App\Http\Middleware\CloseWikisMiddleware;
use App\Http\Middleware\OpenWikisMiddleware;
use App\Http\Middleware\ManageGlobalUserrightsMiddleware;
use App\Http\Middleware\ManageLocalUserrightsMiddleware;

//Работа с САМИМИ викиями
Route::get('/', [WikisController::class,'index'])->name('index');
Route::get('/create-wiki', [WikisController::class,'create'])->name('wikis.create')
->middleware(CreateWikisMiddleware::class);
Route::post('/store', [WikisController::class,'store'])->name('wikis.store')
->middleware(CreateWikisMiddleware::class);
Route::delete('/destroy/{wiki}', [WikisController::class,'destroy'])->name('wikis.destroy')
->middleware(CloseWikisMiddleware::class);
Route::post('/open/{wiki}', [WikisController::class,'open'])->name('wikis.open')
->middleware(OpenWikisMiddleware::class);
Route::get('/closed-wikis', [WikisController::class,'show_closed_wikis'])->name('wikis.show_closed')
->middleware(OpenWikisMiddleware::class);
Route::get('/global-user-rights/{userId}', [WikisController::class,'manage_global_user_rights'])->name('wikis.global_userrights')
->middleware(ManageGlobalUserrightsMiddleware::class);
Route::post('/global-user-rights/{userId}/store', [WikisController::class,'store_global_user_rights'])->name('wikis.global_userrights.store')
->middleware(ManageGlobalUserrightsMiddleware::class);

//Работа со статьями (и правами) на викиях
Route::get('/wiki/{wikiName}/all-articles', [ArticleController::class,'index'])->name('index.articles');
Route::get('/wiki/{wikiName}/article/{articleName}', [ArticleController::class,'show_article'])->name('articles.show');
Route::get('/wiki/{wikiName}/article/{articleName}/revision/{revisionId}', [RevisionController::class,'view'])->name('revision.show');
Route::get('/wiki/{wikiName}/article/{articleName}/history', [ArticleController::class,'history'])->name('articles.history');
Route::get('/wiki/{wikiName}/article/{articleName}/edit', [ArticleController::class,'edit'])->name('articles.edit');
Route::get('/wiki/{wikiName}/create-article', [ArticleController::class,'create'])->name('articles.create');
Route::post('/wiki/{wikiName}/store', [ArticleController::class,'store'])->name('articles.store');
Route::post('/wiki/{wikiName}/update/{articleName}/edit', [ArticleController::class,'update'])->name('articles.update');
Route::delete('/wiki/{wikiName}/{articleName}/destroy', [ArticleController::class,'destroy'])->name('articles.destroy')
->middleware(DeleteMiddleware::class);
Route::get('/wiki/{wikiName}/trash', [ArticleController::class,'trash'])->name('articles.trash')
->middleware(ViewDeletedMiddleware::class);//'articles.trash.show'
Route::get('/wiki/{wikiName}/trash/article/{articleName}', [ArticleController::class,'show_deleted_article'])
->name('articles.trash.show')
->middleware(ViewDeletedMiddleware::class);
Route::get('/wiki/{wikiName}/trash/article/{articleName}/history', [ArticleController::class,'show_deleted_article_history'])
->name('articles.deleted.history')
->middleware(ViewDeletedMiddleware::class);
Route::get('/wiki/{wikiName}/article/{articleName}/deleted_history', [ArticleController::class,'deleted_history'])
->name('articles.trash.edits')
->middleware(ViewDeletedRevisionsMiddleware::class);
Route::post('/wiki/{wikiName}/{articleName}/restore', [ArticleController::class,'restore'])->name('articles.restore')
->middleware(RestoreMiddleware::class);
Route::delete('/wiki/{wikiName}/{articleName}/{revisionId}/destroy', [RevisionController::class,'destroy'])->name('revision.delete')
->middleware(DeleteRevisionMiddleware::class);
Route::post('/wiki/{wikiName}/{articleName}/{revisionId}/restore', [RevisionController::class,'restore'])->name('revision.restore')
->middleware(RestoreRevisionMiddleware::class);

Route::get('/wiki/{wikiName}/user-rights/{userId}', [WikisController::class,'manage_local_user_rights'])->name('wikis.local_userrights')
->middleware(ManageLocalUserrightsMiddleware::class);
Route::post('/wiki/{wikiName}/user-rights/{userId}/store', [WikisController::class,'store_local_user_rights'])->name('wikis.local_userrights.store')
->middleware(ManageLocalUserrightsMiddleware::class);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

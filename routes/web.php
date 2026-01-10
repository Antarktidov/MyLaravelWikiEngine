<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WikisController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\RevisionController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserRightsController;
use App\Http\Controllers\CommentsController;

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

use App\Http\Middleware\DeleteImagesMiddleware;

use App\Http\Middleware\DeleteCommentsMiddleware;


//Approve feature middlewares
use App\Http\Middleware\ApproveRevisionMiddleware;

//Работа с САМИМИ викиями
Route::get('/', [WikisController::class,'index'])->name('index');
Route::get('/create-wiki', [WikisController::class,'create'])->name('wikis.create')
->middleware(CreateWikisMiddleware::class);
Route::post('/store', [WikisController::class,'store'])->name('wikis.store')
->middleware(CreateWikisMiddleware::class);
Route::delete('/destroy/{wiki}', [WikisController::class,'destroy'])->name('wikis.destroy')
->middleware(CloseWikisMiddleware::class);
Route::post('/open/{wiki}', [WikisController::class,'restore'])->name('wikis.open')
->middleware(OpenWikisMiddleware::class);
Route::get('/closed-wikis', [WikisController::class,'trash'])->name('wikis.show_closed')
->middleware(OpenWikisMiddleware::class);

//Работа с правами на вики
//1. Глобальными
Route::get('/global-user-rights/{userId}', [UserRightsController::class,'manage_global_user_rights'])->name('wikis.global_userrights')
->middleware(ManageGlobalUserrightsMiddleware::class);
Route::post('/global-user-rights/{userId}/store', [UserRightsController::class,'store_global_user_rights'])->name('wikis.global_userrights.store')
->middleware(ManageGlobalUserrightsMiddleware::class);
//2. Локальными
Route::get('/wiki/{wikiName}/user-rights/{userId}', [UserRightsController::class,'manage_local_user_rights'])->name('wikis.local_userrights')
    ->middleware(ManageLocalUserrightsMiddleware::class);
Route::post('/wiki/{wikiName}/user-rights/{userId}/store', [UserRightsController::class,'store_local_user_rights'])->name('wikis.local_userrights.store')
    ->middleware(ManageLocalUserrightsMiddleware::class);

//Работа с общими изображениями
Route::get('/commons/upload', [ImageController::class,'create'])->name('images.upload_page');
Route::post('/commons/store', [ImageController::class,'store'])->name('images.store');
Route::get('/commons', [ImageController::class,'index'])->name('images.gallery');
Route::delete('/commons/delete/{image}', [ImageController::class,'destroy'])->name('images.delete')
->middleware(DeleteImagesMiddleware::class);

//Работа со статьями на викиях
Route::get('/wiki/{wikiName}/all-articles', [ArticleController::class,'index'])->name('index.articles');
Route::get('/wiki/{wikiName}/article/{articleName}', [ArticleController::class,'show'])->name('articles.show');
Route::get('/wiki/{wikiName}/article/{articleName}/edit', [ArticleController::class,'edit'])->name('articles.edit');
Route::get('/wiki/{wikiName}/create-article', [ArticleController::class,'create'])->name('articles.create');
Route::post('/wiki/{wikiName}/store', [ArticleController::class,'store'])->name('articles.store');
Route::post('/wiki/{wikiName}/update/{articleName}/edit', [ArticleController::class,'update'])->name('articles.update');
Route::delete('/wiki/{wikiName}/{articleName}/destroy', [ArticleController::class,'destroy'])->name('articles.destroy')
->middleware(DeleteMiddleware::class);
Route::get('/wiki/{wikiName}/trash', [ArticleController::class,'trash'])->name('articles.trash')
->middleware(ViewDeletedMiddleware::class);
Route::get('/wiki/{wikiName}/trash/article/{articleName}', [ArticleController::class,'show_deleted'])
->name('articles.trash.show')
->middleware(ViewDeletedMiddleware::class);
Route::post('/wiki/{wikiName}/{articleName}/restore', [ArticleController::class,'restore'])->name('articles.restore')
    ->middleware(RestoreMiddleware::class);

//Работа с историей правок
Route::get('/wiki/{wikiName}/article/{articleName}/history', [RevisionController::class,'index'])->name('articles.history');
Route::get('/wiki/{wikiName}/trash/article/{articleName}/history', [RevisionController::class,'show_deleted_hist'])
->name('articles.deleted.history')
->middleware(ViewDeletedMiddleware::class);
Route::get('/wiki/{wikiName}/article/{articleName}/deleted_history', [RevisionController::class,'trash'])
->name('articles.trash.edits')
->middleware(ViewDeletedRevisionsMiddleware::class);
Route::delete('/wiki/{wikiName}/{articleName}/{revisionId}/destroy', [RevisionController::class,'destroy'])->name('revision.delete')
->middleware(DeleteRevisionMiddleware::class);
Route::post('/wiki/{wikiName}/{articleName}/{revisionId}/restore', [RevisionController::class,'restore'])->name('revision.restore')
->middleware(RestoreRevisionMiddleware::class);
Route::get('/wiki/{wikiName}/article/{articleName}/revision/{revisionId}', [RevisionController::class,'view'])->name('revision.show');
Route::post('/wiki/{wikiName}/{articleName}/{revisionId}/approve', [RevisionController::class,'approve'])->name('revision.approve')
->middleware(ApproveRevisionMiddleware::class);

//Работа с комментариями под статьями
Route::get('/api/wiki/{wikiName}/article/{articleName}/comments', [CommentsController::class,'show_comments_under_article'])->name('comments.show_all');
Route::post('/api/wiki/{wikiName}/article/{articleName}/comments/store', [CommentsController::class,'store'])->name('comments.store');
Route::delete('/api/wiki/{wikiName}/article/{articleName}/comments/{comment}/delete', [CommentsController::class,'delete'])
->middleware(DeleteCommentsMiddleware::class)
->name('comments.delete');
Route::post('/api/wiki/{wikiName}/article/{articleName}/comments/{comment}/update', [CommentsController::class,'update'])
->name('comments.update');
//Логин, регистрация
Auth::routes(['verify' => true]);

// Email verification routes
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [App\Http\Controllers\Auth\VerificationController::class, 'show'])->name('verification.notice');
    Route::post('/email/resend', [App\Http\Controllers\Auth\VerificationController::class, 'resend'])->name('verification.resend');
});

Route::middleware(['auth', 'signed'])->group(function () {
    Route::get('/email/verify/{id}/{hash}', [App\Http\Controllers\Auth\VerificationController::class, 'verify'])->name('verification.verify');
});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

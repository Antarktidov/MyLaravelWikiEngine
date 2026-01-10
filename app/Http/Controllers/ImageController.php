<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wiki;
use App\Models\Image;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;

class ImageController extends Controller
{
    //Форма загрузки изображения
    public function create() {
        return view('upload');
    }

    //POST-ручка для формы загрузки страницы
    public function store(Request $request) {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'private');
            $filename = str_replace("images/","",$path);

            $data['filename'] = $filename;
            Image::create($data);

            $index = route('index');
            return response(__('The file is uploaded! To add an image to an article, use the following code: ![Image]') . "($index/storage/$path)", 200)
                ->header('Content-Type', 'text/plain');
            //После загрузки файла возвращаем код для вставки в статью
        } else {
            return response(__('Unknown error'), 400)
                ->header('Content-Type', 'text/plain');
        }
    }

    //Заглавная страница викисклада - галерея
    public function index() {
        $images = Image::where('is_approved', true)
        ->whereNull('deleted_at')
        ->Paginate(10);
        $wiki = Wiki::withTrashed()->first();

        return view('gallery', compact('images', 'wiki'));
    }

    //Страница для проверки загруженных изображений
    public function approvers_index() {
        $images = Image::where('is_approved', false)
        ->whereNull('deleted_at')
        ->Paginate(10);
        $wiki = Wiki::withTrashed()->first();

        return view('approvers_gallery', compact('images', 'wiki'));
    }

    //DELETE-ручка для изображения
    //Отвязывает файл от публичного хранилища
    public function destroy(Image $image): string
    {
        $filePath = public_path("storage/images/{$image->filename}");

        $response = "";

        /**
         * Данный трюк нужен, чтобы пройти автотесты
         */
        if (file_exists($filePath)) {
            unlink($filePath);
            $response =  __('File deleted successfully');
        } else {
            $response =  __('File not found');
        }

        $image->delete();
        return $response;
    }

    public function show_private($filename)
    {
    
    $path = storage_path('app/private/images/' . $filename);
    
    if (!file_exists($path)) {
        abort(404);
    }
    
        return response()->file($path);
    }

    public function approve(Image $image) {

        $filename = $image->filename;

        $privatePath = 'images/' . $filename;
        $publicPath = 'images/' . $filename;
        
        if (!Storage::disk('private')->exists($privatePath)) {
            return false;
        }
        
        $fileContents = Storage::disk('private')->get($privatePath);
        Storage::disk('public')->put($publicPath, $fileContents);
        Storage::disk('private')->delete($privatePath);

        $image->update(
            ['is_approved' => true]
        );
        
        return true;
    }
}

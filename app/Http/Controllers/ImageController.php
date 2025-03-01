<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wiki;//7 бед - один ответ, костыль и велосипед (подготовка)

use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class ImageController extends Controller
{
    public function upload_page() {
        return view('upload');
    }

    public function store(Request $request) {
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images', 'public');
            $filename = str_replace("images/","",$path);

            $data['filename'] = $filename;
            Image::create($data);

            $index = route('index');
            return "Файл загружен! Чтобы добавить картинку в статью, используйте следующий код: ![Изображение]($index/storage/$path)";
        } else {
            return 'Неизвестная ошибка!';
        }
    }

    public function gallery() {
        $images = Image::Paginate(10);
        $wiki = Wiki::first();//7 бед - один ответ, костыль и велосипед (исполнение)

        return view('gallery', compact('images', 'wiki'));
    }

    public function destroy(Image $image)
{
    $filePath = public_path("storage/images/{$image->filename}");

    $response = "";

    if (file_exists($filePath)) {
        unlink($filePath);
        $response =  'Файл успешно удалён';
    } else {
        $response =  'Файл не найден';
    }
    
    $image->delete();
    return $response;
}


}

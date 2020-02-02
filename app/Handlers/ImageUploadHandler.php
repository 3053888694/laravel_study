<?php

namespace App\Handlers;

use Psy\Util\Str;
use Image;

class ImageUploadHandler
{
    protected $allow_ext = [
        'png', 'jpg', 'gif', 'jpeg'
    ];

    public function save($file, $folder, $file_prefix, $max_width = false)
    {
        $folder_name = "uploads/images/$folder/" . date('Y/m/d', time());

        $upload_path = public_path() . '/' . $folder_name;

        $ext = strtolower($file->getClientOriginalExtension()) ?: 'png';

        $filename = $file_prefix . '_' . time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $ext;

        if (!in_array($ext, $this->allow_ext)) {
            return false;
        }

        $file->move($upload_path, $filename);

        if ($max_width && $ext != 'gif') {
            $this->reduceSize($upload_path . '/' . $filename, $max_width);
        }

        return [
            'path' => config('app.url') . "/$folder_name/$filename"
        ];
    }

    public function reduceSize($filepath, $max_width)
    {
        $image = Image::make($filepath);

        $image->resize($max_width, null, function ($constraint){

            $constraint->aspectRatio();

            $constraint->upsize();
        });

        $image->save();
    }
}

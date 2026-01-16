<?php

namespace App\Traits;

trait WithSaveFile
{
    public function saveFile($file, $path = 'file', $base_file_name = 'file')
    {
        // Set base file name
        $base_file_name = $base_file_name.'_'.date('d-m-Y').'_';

        // If file is not exist
        if (! $file) {
            return false;
        }

        $filename = $base_file_name.uniqid().'.'.$file->extension();
        $file->storeAs($path, $filename);

        return $path.'/'.$filename;
    }
}

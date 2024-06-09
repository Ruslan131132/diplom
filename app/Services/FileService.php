<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class FileService
{

    public function save(UploadedFile $file, $dir = '', $name = null): string
    {
        $dir = trim($dir ?? '', '/');
        if (!empty($dir) && !file_exists(storage_path('app/public') . '/' . $dir)) {
            mkdir(storage_path('app/public') . '/' . $dir, 0777, true);
        }
        if (empty($name)) {
            $name = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . Str::random(5) . '_' . time() . '.' . $file->getClientOriginalExtension();
        }
        $file->storeAs('public/' . (!empty($dir) ? $dir . '/' : '') . str_replace(' ', '_', $name));

        return config('app.domain_url') . '/storage/' . (!empty($dir) ? $dir . '/' : '') . str_replace(' ', '_', $name);
    }

    public function delete($path): bool
    {
        return Storage::delete('public/' . str_replace(config('app.domain_url') . '/storage/', '', $path));
    }
}

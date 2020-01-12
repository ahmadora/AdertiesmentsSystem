<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;
class FilesHelper
{
    const ADVERTISEMENT = 'advertisement';

    const DIRECTORIES = [
        FilesHelper::ADVERTISEMENT => 'advertisements',
    ];

    public static function saveFile($file, string $type) {
        $file_name = uniqid() . '.' . File::extension($file->getClientOriginalName());
        $file->move(self::getFileDirectoryPath($type), $file_name);
        return $file_name;
    }

    public static function removeFile(string $type, string $file_name) {
        File::delete([self::getFilePath($type, $file_name)]);
    }

    public static function getFilePath(string $type, string $file_name) {
        return self::getFileDirectoryPath($type) . DIRECTORY_SEPARATOR . $file_name;
    }

    public static function getFileURL(string $type, string $file_name) {
        return url(self::getFilePreURL($type) . '/' . $file_name);
    }

    private static function getFileDirectoryPath(string $type) {
        return self::DIRECTORIES[$type] . DIRECTORY_SEPARATOR;
    }

    public static function getFilePreURL(string $type) {
        return url('/' . self::DIRECTORIES[$type]);
    }
}
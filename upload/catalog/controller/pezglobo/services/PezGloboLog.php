<?php

final class PezGloboLog {
    public static function delete($fileToDelete) {
        $fileToDelete = DIR_LOGS . $fileToDelete;
        if (!file_exists($fileToDelete)) {
            return;
        }
        if (!unlink($fileToDelete)) {
            $log = new Log('CacheWriter');
            $log->write("Unable to delete the file.");
        }
    }

    public static function write($file, $str)
    {
        self::writeStr($file, $str);
    }

    private static function writeStr($cacheFile, $str)
    {
        $cacheFile = DIR_LOGS . $cacheFile;
        file_put_contents($cacheFile, $str);
    }

    public static function read($file)
    {
        $file = DIR_LOGS . $file;
        if (file_exists($file) && is_readable($file)) {
            return (int) file_get_contents($file);
        }
        
        return '';
    }
}
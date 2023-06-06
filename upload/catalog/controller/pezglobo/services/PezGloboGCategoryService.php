<?php

final class PezGloboGCategoryService 
{
    public static function get(string $id) {
        $file = DIR_APPLICATION . 'controller' . DIRECTORY_SEPARATOR . 'pezglobo' . DIRECTORY_SEPARATOR . 'taxonomy.csv';

        if (($handler = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handler, 1000, ",")) !== FALSE) {
                if ($data[0] == $id) {
                    unset($data[0]);
                    fclose($handler);
                    return implode(",", $data);
                }
            }
            fclose($handler);
        }
    
        return null;
    }
}
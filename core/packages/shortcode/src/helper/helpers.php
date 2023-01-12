<?php

if (!function_exists('get_all_short_code_in_view')) {
    function get_all_short_code_in_view(): array
    {
        $result = [];
        if(File::isDirectory(resource_path('views/vendor/bo/shortcodes'))){
            $files = File::allFiles(resource_path('views/vendor/bo/shortcodes'));
            foreach ($files as $file){
                if($file->isFile()){
                    $file_name = str_replace(".blade.php", "", $file->getFilename());
                    $result["bo::shortcodes.$file_name"] = "views/vendor/bo/shortcodes.{$file->getFilename()}";
                }
            }
        }
        return $result;
    }
}

if (!function_exists('get_all_short_codes')){
    function get_all_short_codes()
    {
        return \Bo\Shortcode\App\Models\Shortcode::where('deleted_at', null)->get();
    }
}

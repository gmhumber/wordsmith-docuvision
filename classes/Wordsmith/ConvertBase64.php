<?php

namespace App\Wordsmith;

class ConvertBase64 {

    private function __construct() {
    }

    //This function converts a file into a Base64 string for use with the Google APIs
    // @param $filePath -> full file path to the source file to be converted by the function
    // returns Base64 string representation of the source file
    public static function toBase64String($filePath) {
        $newBase64String = base64_encode(file_get_contents($filePath));
        return $newBase64String;  
    }

}


?>

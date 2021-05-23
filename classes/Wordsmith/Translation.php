<?php

namespace App\Wordsmith;

class Translation {

    private function __construct() {        
    }


    // This function calls the Google Cloud Translate API to perform a translation of the source text into the targeted language
    // @param $sourceText -> the original source text
    // @param $sourceLanguageCode -> ISO-639-1 language code of the source text language
    // @param $targetLanguageCode -> ISO-639-1 language code of the targeted language for translation
    // @param $googleAccessToken -> Google Application Credentials token
    // Returns a JSON object containing the translated source text
    public static function getTranslation($sourceText, $sourceLanguageCode, $targetLanguageCode, $googleProjectNumberOrId, $googleAccessToken) {

        $googleTranslateUrl = "https://translation.googleapis.com/v3/projects/$googleProjectNumberOrId:translateText";
    
        $requestBodyArr = [
            "sourceLanguageCode" => $sourceLanguageCode,
            "targetLanguageCode" => $targetLanguageCode,
            "contents" => ["$sourceText"],
            "mimeType" => "text/plain"
        ];
    
        $requestBodyJson = json_encode($requestBodyArr);
    
        //Set array to hold options to be passed to the CURL command
        $curlOptions = [
            CURLOPT_HTTPHEADER => [ "Authorization: Bearer $googleAccessToken",
                                    'Content-Type: application/json; charset=utf-8' ],
            CURLOPT_URL => $googleTranslateUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestBodyJson,
            //Set option to return data from the CURL command
            CURLOPT_RETURNTRANSFER => true        
        ];
    
        try {
            $curlSession = curl_init();
            curl_setopt_array($curlSession, $curlOptions);
            $result = json_decode(curl_exec($curlSession));
            curl_close($curlSession);
            return $result;
        } 
        catch (\Exception $error){
            return "Error: translation request failed, $error";
        }
    }

}

?>

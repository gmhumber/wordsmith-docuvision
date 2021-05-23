<?php

namespace App\Wordsmith;

class Ocr {

    private function __construct() {
    }

    // This function calls the Google Cloud Vision API to perform an OCR conversion procedure on a PDF, TIFF or GIF file
    // @param $base64DocumentString -> Base64 representation of the source file
    // @param $fileMimeType -> Mime type of the source file
    // @param $ocrTextType -> choose between "TEXT_DETECTION" for low text density files and "DOCUMENT_TEXT_DETECTION" for high text density files
    // @param $googleAccessToken -> Google Application Credentials token
    // Returns a JSON object containing the OCR converted text of the source file
    public static function getOcr($base64DocumentString, $fileMimeType, $ocrTextType, $googleAccessToken) {

        $googleCloudVisionUrl = 'https://vision.googleapis.com/v1/files:annotate';
    
        $requestBodyArr = [
            "requests" => [
                [
                    "inputConfig" => [
                        "content" => $base64DocumentString,
                        "mimeType" => $fileMimeType
                    ],
                    "features" => [
                        [
                            "type" => $ocrTextType
                        ]
                    ]
                ]
            ]
        ];
    
        $requestBodyJson = json_encode($requestBodyArr);
    
        //Set array to hold options to be passed to the CURL command
        $curlOptions = [
            CURLOPT_HTTPHEADER => [ "Authorization: Bearer $googleAccessToken",
                                    'Content-Type: application/json; charset=utf-8' ],
            CURLOPT_URL => $googleCloudVisionUrl,
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
            return "Error: OCR conversion request failed, $error";
        }
    }

}

?>
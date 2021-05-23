<?php

namespace App\Wordsmith;

class Sentiment {

    private function __construct() {        
    }

    // This function calls the Google Cloud Natural Language API to perform a sentiment analysis of the source text
    // @param $sourceText -> the original source text
    // @param $sourceLanguageCode -> ISO-639-1 language code of the source text language
    // @param $googleAccessToken -> Google Application Credentials token
    // Returns a JSON object containing the sentiment assessment
    public static function gaugeSentiment($sourceText, $sourceLanguageCode, $googleAccessToken) {

        $googleSentimentAnalysisUrl = "https://language.googleapis.com/v1/documents:analyzeSentiment";
    
        $requestBodyArr = [
            'encodingType' => 'UTF8',
            'document' => [
                'type' => 'PLAIN_TEXT',
                'language' => $sourceLanguageCode,
                'content' => $sourceText
            ]
        ];
    
        $requestBodyJson = json_encode($requestBodyArr);
    
        //Set array to hold options to be passed to the CURL command
        $curlOptions = [
            CURLOPT_HTTPHEADER => [ "Authorization: Bearer $googleAccessToken",
                                    'Content-Type: application/json; charset=utf-8' ],
            CURLOPT_URL => $googleSentimentAnalysisUrl,
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
            return "Error: sentiment analysis request failed, $error";
        }
    }


    // This function return HTML code to create a meter to represent the overall sentiment of the source text
    // @param $sentimentValue -> the numeric value assigned to the text's sentiment within a range of -100 and 100, negative numbers represent negative sentiment and positive numbers represent positive sentiment
    //returns the HTML code for a sentiment meter bar
    public static function getSentimentMeter($sentimentValue) {
        
        if (is_nan($sentimentValue) 
            || $sentimentValue < -100
            || $sentimentValue > 100) {
            return "Error: sentiment value must be between -100 and 100";
        } 

        if ($sentimentValue < 0) {
            $barColor = "red";
        } else {
            $barColor = "green";
        }

        $absoluteSentimentValue = strval(abs($sentimentValue));

        $meterDiv = <<<METERHTML
            <div id="meterdiv" class="d-md-inline-block align-middle">
                <div id="meterbar" style="width:$absoluteSentimentValue%; background-color:$barColor"></div>
            </div>
        METERHTML;

        return $meterDiv;


    }

}



?>
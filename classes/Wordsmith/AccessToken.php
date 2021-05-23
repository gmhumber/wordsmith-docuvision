<?php

namespace App\Wordsmith;

use Google_Client;

class AccessToken {

    private function __construct() {
        require_once '../../vendor/autoload.php';
    }


    // This function executes a shell command which calls the Google Cloud SDK to obtain an access token. The SDK must be installed on the local machine, the private key JSON file must reside on the local file system, and the path to the private key JSON file must be set as an enviromental variable using "set GOOGLE_APPLICATION_CREDENTIALS="[PATH_TO_PRIVATE_KEY_JSON_FILE]"" This function will set the envirometnal variable with a default path to the private key file, or the default value can be overridden if desired.
    // @param $privateKeyPath -> path to Google access private key JSON file
    // returns Google API access token
    public static function getGoogleAccessToken($privateKeyPath = 'VisionApiAccessAccountKey/wordsmith-310900-ba63a5935046.json') {
        $accessToken = shell_exec("set GOOGLE_APPLICATION_CREDENTIALS=$privateKeyPath && gcloud auth application-default print-access-token");
        $accessToken = substr($accessToken, 0, 223); //Take only the first 223 characters of the returned key string, the last character is caused by an automatic carriage return and must be striped out to obtain the valid key
        return $accessToken;
    }


    // This function obtains the Google service account access token programmatically without using the Google SDK
    // @param $privateKeyPath -> path to Google access private key JSON file
    // returns Google API access token
    public static function getGoogleAccessTokenWithoutSdk($privateKeyPath = 'VisionApiAccessAccountKey/wordsmith-310900-ba63a5935046.json') {
        $client = new Google_Client();
        $client->setAuthConfig($privateKeyPath);
        $client->addScope( ['https://www.googleapis.com/auth/cloud-translation', 'https://www.googleapis.com/auth/cloud-vision', 'https://www.googleapis.com/auth/cloud-language'] );
        $client->fetchAccessTokenWithAssertion();
        $accessTokenArray = $client->getAccessToken();
        $accessToken = $accessTokenArray['access_token'];
        return $accessToken;
    }

}


?>
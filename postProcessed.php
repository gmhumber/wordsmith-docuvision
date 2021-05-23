<?php

require_once './vendor/autoload.php';

use App\Wordsmith\{AccessToken, ConvertBase64, Ocr, Sentiment, Translation};

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();



// Validate form input and source document attributes are in acceptable form
if (!isset($_POST['textdensity'])
    || empty($_POST['textdensity'])
    || !isset($_POST['targetlanguage'])
    || empty($_POST['targetlanguage'])
    || !isset($_FILES['sourcedocument']) 
    || $_FILES['sourcedocument']['size'] <= 0
    || !($_FILES['sourcedocument']['type'] === 'application/pdf' 
        || $_FILES['sourcedocument']['type'] === 'image/tiff'
        || $_FILES['sourcedocument']['type'] === 'image/gif')) {

        Header('Location: index.php?error=true');
        exit;
    }


// Set the Google OCR text detection option based on the user-provided text-density assessment
if ($_POST['textdensity'] === "low") {
    $ocrTextOption = "TEXT_DETECTION";
} 
else if ($_POST['textdensity'] === "high") {
    $ocrTextOption = "DOCUMENT_TEXT_DETECTION";
}
else {
    Header('Location: index.php?error=true');
    exit;
}





//Convert the source file to a Base64 string as required by the Google Vision OCR API
$base64SourceString = ConvertBase64::toBase64String($_FILES['sourcedocument']['tmp_name']);

$sourceMimeType = $_FILES['sourcedocument']['type'];
$translationTargetLanguage = $_POST['targetlanguage'];
$googleProjectNumber = $_ENV['GOOGLE_PROJECT_NUMBER'];

//Get Google access token with getGoogleAccessToken() function, this function uses the Google SDK
//$accessToken = AccessToken::getGoogleAccessToken(); //this function uses the Google SDK

// Get Google API Access Token without using the Google SDK
$accessToken = AccessToken::getGoogleAccessTokenWithoutSdk();

//Get OCR converted text by calling the getOcr() function
$ocrResponse = Ocr::getOcr($base64SourceString, $sourceMimeType, $ocrTextOption, $accessToken);

//Get source text's language code
$ocrResponseLanguageCode = $ocrResponse->responses[0]->responses[0]->fullTextAnnotation->pages[0]->property->detectedLanguages[0]->languageCode;

//Get OCR processed version of the source text
$ocrResponseText = $ocrResponse->responses[0]->responses[0]->fullTextAnnotation->text; 


//Translate the source text if the source language and the translation target language are not the same, otherwise no translation is required
if ($ocrResponseLanguageCode !== $translationTargetLanguage) {

    //Get translated text by calling getTranslation() function
    $translatedResult = Translation::getTranslation($ocrResponseText, $ocrResponseLanguageCode, $translationTargetLanguage, $googleProjectNumber, $accessToken);

    //Assign the translated text to a variable
    $translatedText = $translatedResult->translations[0]->translatedText;

} else {

    $translatedText = $ocrResponseText;

}


//Get document sentiment analysis score by calling the gaugeSentiment() function
$sentimentResult = Sentiment::gaugeSentiment($ocrResponseText, $ocrResponseLanguageCode, $accessToken);

//Get the sentiment score
$sentimentScore = floatval($sentimentResult->documentSentiment->score);

//Get the sentiment magnitude
$sentimentMagnitude = floatval($sentimentResult->documentSentiment->magnitude);


//Analyze the sentiment score and magnitude results from Google then assign a sentiment value judgement to the result in English
if ($sentimentScore >= 0.7) {

    if ($sentimentMagnitude >= 4) {
        $sentimentDescription = 'extremely positive';
        $sentimentMeter = Sentiment::getSentimentMeter(100);
    } else if ($sentimentMagnitude >= 1 && $sentimentMagnitude < 4) {
        $sentimentDescription = 'positive';
        $sentimentMeter = Sentiment::getSentimentMeter(75);
    } else {
        $sentimentDescription = 'mildly positive';
        $sentimentMeter = Sentiment::getSentimentMeter(50);
    }

} else if ($sentimentScore >= 0.2 && $sentimentScore < 0.7 ) {

    $sentimentDescription = "somewhat positive";
    $sentimentMeter = Sentiment::getSentimentMeter(25);

} else if ($sentimentScore <= -7) {

    if ($sentimentMagnitude >= 4) {
        $sentimentDescription = 'extremely negative';
        $sentimentMeter = Sentiment::getSentimentMeter(-100);
    } else if ($sentimentMagnitude >= 1 && $sentimentMagnitude < 4) {
        $sentimentDescription = 'negative';
        $sentimentMeter = Sentiment::getSentimentMeter(-75);
    } else {
        $sentimentDescription = 'mildly negative';
        $sentimentMeter = Sentiment::getSentimentMeter(-50);
    }

} else if ($sentimentScore <= -0.2 && $sentimentScore > -0.7) {

    $sentimentDescription = "somewhat negative";
    $sentimentMeter = Sentiment::getSentimentMeter(-25);

} else if ($sentimentScore > -0.2 &&  $sentimentScore < 0.2) {

    if ($sentimentMagnitude >= 3) {
        $sentimentDescription = 'mixed sentiment';
        $sentimentMeter = Sentiment::getSentimentMeter(0);
    } else {
        $sentimentDescription = 'neutral sentiment';
        $sentimentMeter = Sentiment::getSentimentMeter(0);
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous" />
    <link rel="stylesheet" href="styles/styles.css" />
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <title>WordSmith</title>
</head>
    <body>
        <header class="container-fluid mb-5">
            <div class="row justify-content-center">
                <h2><a href="index.php"><img id="logoimg" src="images/logo.png" alt="Wordsmith logo" /></a></h2>
            </div>
            <h2 class="sr-only">WordSmith</h2>
        </header>
        <main class="container">
            <div class="mb-4">
                <h1>Docu<span class="orangespan">V</span>ision</h1>
            </div>  
            <div class="row mb-4">
                <div class="col-md-12">
                    <h3 id="sentiment-title" class="d-md-inline-block">Sentiment Gauge</h3>
                    <?= $sentimentMeter ?>
                    <p>The overall sentiment of the source text is <span class="font-weight-bold"><?= $sentimentDescription ?></span>.</p>

                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h3 class="mb-3">Source Text (OCR)</h3>
                    <pre><?= $ocrResponseText ?></pre>
                </div>
                <div class="col-md-6">
                    <h3 class="mb-3">Translated Text</h3>
                    <pre><?= $translatedText ?></pre>
                </div>
            </div>
        </main>
    </body>
</html>
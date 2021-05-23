<?php

if (isset($_GET['error']) &&  $_GET['error'] === 'true') {

    $errorFlag = true;

    $errorDiv = <<<ERRORHTML
    <div class="form-group row">
        <div class="col-md-6">
            <p class="text-danger">Looks like your form was incomplete, please try again.</p>
        </div>               
    </div>
    ERRORHTML;
    
} else {

    $errorFlag = false;

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
            <div class="mb-5">
                <p>We created DocuVision to perform Optical Character Recognition (OCR), language translation, and sentiment analysis on your PDF, TIFF and GIF files. Try it by uploading your document or image file.</p>
                <p>Note: DocuVision will process up to 5 pages per request.</p>
            </div>
            <div class="mx-5">
                <form action="postProcessed.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="sourcedocument">Source Document (PDF, TIFF, GIF files only)</label>
                            <input type="file" name="sourcedocument" class="form-control-file" id="sourcedocument">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="textdensity">Text Density (text-to-space ratio)</label>
                            <select name="textdensity" class="form-control" id="textdensity">
                                <option value="" selected>SELECT ONE</option>
                                <option value="low">Low Text Density (e.g. movie poster)</option>
                                <option value="high">High Text Density (e.g. textbook)</option>
                            </select>
                        </div>               
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label for="targetlanguage">Targeted Lanaguage for Translation</label>
                            <select name="targetlanguage" class="form-control" id="targetlanguage">
                                <option value="" selected>SELECT ONE</option>
                                <option value="en">English</option>
                                <option value="fr">French</option>
                                <option value="es">Spanish</option>                        
                                <option value="zh-TW">Chinese (traditional)</option>
                                <option value="zh-CN">Chinese (simplified)</option>
                                <option value="ja">Japanese</option>
                                <option value="ko">Korean</option>
                                <option value="tl">Tagalog</option>
                                <option value="fa">Persian</option>
                                <option value="ar">Arabic</option>
                                <option value="ru">Russian</option>
                            </select>
                        </div>             
                    </div>
                    <?= $errorFlag ? $errorDiv : "" ?>
                    <button type="submit" class="btn btn-primary wordsmith-btn">Submit</button>
                </form>
            </div>  
        </main>
    </body>
</html>
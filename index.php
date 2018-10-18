<?php

namespace App;

require "Main.php";
require "TextToSpeech.php";

$app = new Main();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TextToSpeech Generator</title>

    <link rel="stylesheet" href="/resources/css/app.css" />
</head>
<body>
    <div class="main-container">
        <div id="header"></div>

        <div id="content">
            <div class="container">
                <p><?=$app->testString()?></p>
            </div>
        </div>

        <div id="footer"></div>
    </div>
</body>

<script type="text/javascript" src="/resources/js/app.js"></script>
</html>

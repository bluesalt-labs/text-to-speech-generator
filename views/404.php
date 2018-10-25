<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Page Not Found | TextToSpeech Generator</title>

    <link rel="stylesheet" href="/resources/css/app.css" />
</head>
<body>
<div class="main-container">
    <div id="header">
        <div class="container">
            TextToSpeech Generator
        </div>

    </div>

    <div id="content">
        <p class="error-container-full">
            404 :(

            <?php if(count( $this->getMessages() ) > 0):?>
                <ul class="error-message-container">
                <?php foreach($this->getMessages() as $message):?>
                    <li><?=$message?></li>
                <?php endforeach?>
                </ul>
            <?php endif;?>
        </p>
    </div>

    <div id="footer"></div>
</div>
</body>
</html>
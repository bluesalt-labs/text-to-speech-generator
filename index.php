<?php

namespace App;

define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

require "Main.php";
require "TextToSpeech.php";

$dateNow = new \DateTime();
$app = new Main( $_SERVER['REQUEST_METHOD'], $_REQUEST);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>TextToSpeech Generator</title>

    <link rel="stylesheet" href="/resources/css/app.css?v=<?=$dateNow->format('YmdHis')?>" />
</head>
<body>
    <div class="main-container">
        <div id="header">
            <div class="container">
                TextToSpeech Generator
            </div>

        </div>

        <div id="content">

            <div class="container">

                <form method="POST" action="/" class="container-half" id="text_content_form">
                    <h2>Form</h2>
                    <div class="input-container">
                        <label for="text_content">Text Content</label>
                        <textarea class="v-resize" id="text_content" name="text_content" rows="20"></textarea>
                    </div>

                    <div class="input-container">
                        <label for="voice">Voice</label>
                        <select id="voice" name="voice">
                            <option value="">Select...</option>
                            <?foreach($app->getVoices() as $key => $voice):?>
                            <option value="<?=$key?>">
                                <?=$voice['preferred'] ?  '*' : '' ?>
                                <?=$voice['name']?> |
                                <?=$voice['language']?>
                                <?=$voice['gender'] === 'm' ? ' | Male' : '' ?>
                                <?=$voice['gender'] === 'f' ? ' | Female' : '' ?>
                            </option>
                            <?endforeach;?>
                        </select>
                    </div>

                    <button type="button" class="btn-lg" id="generate_audio_button">Generate</button>
                </form>

                <div class="container-half">
                    <h2>Dictionary Replacements</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Text</th>
                                <th>Replacement</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?foreach($app->getSSMLReplacements() as $replacement => $value):?>
                            <tr>
                                <td class="monospace"><?=htmlspecialchars($replacement)?></td>
                                <td class="monospace"><?=htmlspecialchars($value)?></td>
                            </tr>
                            <?php endforeach?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="container">
                <div class="container-full">
                    <br />
                    <hr />
                    <p>test 2</p>


                </div>

            </div>

        </div>

        <div id="footer"></div>
    </div>
</body>

<script type="text/javascript" src="/resources/js/app.js"></script>
</html>

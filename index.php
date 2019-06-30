<?php
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="pl-PL">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./css/preview.css" />
    <script src="./libs/jquery-3.3.1.min.js"></script>
    <script src="./libs/jquery.form.min.js"></script>
    <script src="./app.js"></script>
    <title>Test generator</title>
</head>

<body>
    <iframe id="downloadFrame" src="" style="display:none; visibility:hidden;"></iframe>


    <ul class="nav">
        <li><a id="ght" href="#">Wygeneruj test html</a></li>
        <li><a id="gpf" href="#">Wygeneruj test pdf</a></li>
        <li><a id="saj" href="#">Zapisz do pliku</a></li>
        <li><a id="lfj" href="#">Załaduj z pliku</a></li>
    </ul>

    <div class="container">
        <form style="display: none" enctype="multipart/form-data" id="loadJsonForm"><input accept=".json" type="file" name="questionJson" id="loadJson" /></form>

        <form id="questioncon"></form>
        <input type="button" class="buttonshadow button addQuestion" id="addQuestionButton" value="Dodaj pytanie" />
    </div>

    <div id="theme_chooser" class="popup">
        <div class="popup-content">
            <div class="popup-theme-choose">
                <div><input type="radio" name="theme" checked theme="light" />Light</div>
                <div><input type="radio" name="theme" theme="dark" />Dark</div>
                <div><input type="radio" name="theme" theme="darker" />Darker</div>
            </div>
            <div class="popup-theme-right">
                <div class="popup-theme-preview">
                    <div class="preview-question">
                        <div class="preview-content">
                            <p>1. Przykładowe pytanie</p>
                        </div>
                        <div class="preview-answers">
                            <div class="preview-answer">
                                <input class="preview-answerbox" answer-pos="1" question-pos="1" type="radio" name="1" />
                                <p><span>a) Przykładowa odpowiedź</span><span></span></p>
                            </div>
                            <div class="preview-answer">
                                <input class="preview-answerbox" answer-pos="2" question-pos="1" type="radio" name="1" />
                                <p><span>b) Przykładowa odpowiedź</span><span></span></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="popup-theme-buttons">
                    <a class="buttonshadow" id="tpcancel" href="#">Anuluj</a>
                    <a class="buttonshadow" id="ghtml" href="#">Wygeneruj</a>
                </div>
            </div>

        </div>
    </div>
</body>

</html>
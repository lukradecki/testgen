$(function () {
    const QUESTIONTEMP = '' +
        '<div question-pos="{{q-pos}}" class="question">' +
            '<div class="quest">' +
                '<img class="delico deletequestion" src="./img/X.svg" alt="Delete">' +
                '<textarea placeholder="Pytanie..." class="content" name="pytania[{{q-num}}][content]"></textarea>' +
                '<input value="+" title="Dodaj pytanie" type="button" class="buttonshadow button addAnswer">'+
            '</div>'+
            '<ol class="answers">'+
            '</ol>'+
        '</div>';
    const ANSWERTEMP = '' +
        '<li answer-pos="{{a-pos}}" class="pun">' +
            '<img class="delico deleteanswer" src="./img/X.svg" alt="Delete">' +
            '<textarea placeholder="Odpowiedź..." class="answer" name="pytania[{{q-num}}][answers][{{a-num}}][content]"></textarea>' +
            '<div><input class="check" type="checkbox" name="pytania[{{q-num}}][answers][{{a-num}}][correct]"></div>' +
        '</li>';
    function addQuestion(JsonObject = null){
        let qid;
        if( $('#questioncon').children().length < 1){
            qid = 0;
        }else{
            qid = Number($($('#questioncon').children()[$('#questioncon').children().length - 1]).attr('question-pos')) +1;
        }
        let question = $(QUESTIONTEMP);

        if(JsonObject != null){
            question.find('.content').val(JsonObject.content)
            for(key in JsonObject.answers){
                if(JsonObject.answers.hasOwnProperty(key)){
                    addAnswer(question, qid,JsonObject.answers[key]);
                }
            }
        }

        

        question.find('.deletequestion').bind('click', function(){
            question.remove()
        });
        question.find('.addAnswer').bind('click', function(){
            addAnswer(question, qid);
        });
        
        question.attr('question-pos', question.attr('question-pos').replace('{{q-pos}}', qid))
        question.find('.content').attr('name', question.find('.content').attr('name').replace('{{q-num}}', qid))
        $('#questioncon').append(question)
    }

    function addAnswer(question, qid, content = null){
        let aid;
        
        if($(question).find('.answers').children().length < 1){
            aid = 0;
        }else{
            aid = Number($($(question).find('.answers').children()[$(question).find('.answers').children().length-1]).attr('answer-pos'))+1;
        }
        let answer = $(ANSWERTEMP);

        if(content != null){
            answer.find('.answer').val(content.content);
            answer.find('.check').prop('checked', content.correct);
        }
        answer.find('.deleteanswer').bind('click', function(){
            answer.remove();
        })

        answer.attr('answer-pos',  answer.attr('answer-pos').replace('{{a-pos}}', aid));
        answer.find('.answer').attr('name', answer.find('.answer').attr('name').replace('{{q-num}}', qid).replace('{{a-num}}', aid));
        answer.find('.check').attr('name', answer.find('.check').attr('name').replace('{{q-num}}', qid).replace('{{a-num}}', aid));
        $(question).find('.answers').append(answer);
    }

    $('#addQuestionButton').click(function (){
        addQuestion();
    })
    function sendFormAjax(switcher = '', theme = 'light'){
        $('#questioncon').ajaxSubmit({
            url: 'manager.php',
            type: 'POST',
            data: {ajax: switcher, theme: theme},
            success: function(data){
                if(switcher == 'html' && data != 'error'){
                    $("#downloadFrame").attr('src', 'download.php?link='+data+'&type=html')
                }else if(switcher == 'pdf' && data != 'error'){
                    $("#downloadFrame").attr('src', 'download.php?link='+data+'&type=pdf')
                }else if(switcher == 'saveJson' && data != 'error'){
                    $("#downloadFrame").attr('src', 'download.php?link='+data+'&type=json')
                }
            }
        });
    }
    function sendFileAjax(){
        $('#loadJsonForm').ajaxSubmit({
            url: 'manager.php',
            type: 'POST',
            data: {ajax: "loadJson"},
            success: function(data){
                if(data != 'error'){
                    let jobject = JSON.parse(data);
                    for(key in jobject.questions){
                        if(jobject.questions.hasOwnProperty(key)){
                            obj = jobject.questions[key];
                            addQuestion(obj);
                        }
                    }
                }
            }
        })
    }

    let theme_chooser = false

    $(window).keypress(function (e) {
        if(e.originalEvent.code == "Escape"){
            if(theme_chooser){
                $('#theme_chooser').css('display', 'none');
                theme_chooser = false;
            }
        }
    });

    $('input[type=radio][name=theme]').change(function (e) { 
        e.preventDefault();
        switch ($(this).attr('theme')) {
            case 'light':
                $('.popup-theme-preview').css('background-color', 'white')
                $('.preview-question').css('border', '3px solid lightblue')
                $('.preview-question').css('color', 'black')
                break;
            case 'dark':
                $('.popup-theme-preview').css('background-color', '#2E2E2E')
                $('.preview-question').css('border', '3px solid #63AEFF')
                $('.preview-question').css('color', 'white')
                break;
            case 'darker':
                $('.popup-theme-preview').css('background-color', '#171717')
                $('.preview-question').css('border', '3px solid white')
                $('.preview-question').css('color', '#E0E0E0')
                break;
                
            default:
                break;
        }
    });
    

    $('#ght').click(function (e) { 
        e.preventDefault();
        $('#theme_chooser').css('display', 'flex');
        theme_chooser = true;
        return false;
    });
    $('#ghtml').click(function (e) { 
        e.preventDefault();
        sendFormAjax('html', $('input[type=radio][name=theme]:checked').attr('theme'));
        $('#theme_chooser').css('display', 'none');
        theme_chooser = false;
    });
    $('#tpcancel').click(function (e) { 
        e.preventDefault();
        $('#theme_chooser').css('display', 'none');
        theme_chooser = false;
    });
    $("#gpf").click(function (e) { 
        e.preventDefault();
        sendFormAjax('pdf');
    });
    $('#lfj').click(function (e) { 
        e.preventDefault();
        $('#loadJson').click();
    });
    $("#loadJsonForm").change(function (e) { 
        e.preventDefault();
        sendFileAjax();
    });
    $('#saj').click(function (e) { 
        e.preventDefault();
        sendFormAjax('saveJson')
    });
});
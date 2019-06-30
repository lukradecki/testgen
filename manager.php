<?php
if(session_status() != PHP_SESSION_ACTIVE){
    session_start();
}
function makeQuizObject($pytania){
    require_once 'phpl/Quiz.php';
    if(isset($pytania)){
        $Quiz = new Quiz();
        foreach ($pytania as $pytanie) {
            $que = new Question($pytanie['content']);
            if(isset($pytanie['answers'])){
                foreach ($pytanie['answers'] as $answer) {
                    $ans = new Answer($answer['content']);
                    isset($answer['correct']) ? $ans->setCorrect() : $ans;
                    $que->addAnswer($ans);
                }
            }
            $Quiz->addQuestion($que);  
        }
        return $Quiz;
    }

}

if(isset($_POST['ajax'])){
    if(!(file_exists("workarea") && is_dir("workarea")))
        mkdir('workarea');

    if($_POST['ajax'] == 'html'  && isset($_POST['pytania']) && gettype($_POST['pytania']) == 'array' && count($_POST['pytania']) > 0){
        require_once 'phpl/Quiz.php';

        

        $TESTTEMPLATE = file_get_contents('template/test.html');


        $Quiz = makeQuizObject($_POST['pytania']);
        
        
        if(isset($_POST['theme'])){
            $theme = $_POST['theme'];
        }else{
            $theme = 'light';
        }
        $style = '';

        if(file_exists("template/$theme.css") && !is_dir("workarea/$theme.css")){
            $style = file_get_contents("template/$theme.css");
        }

        $TESTTEMPLATE = str_replace('{{questions}}', $Quiz->getHTML(), $TESTTEMPLATE);
        $TESTTEMPLATE = str_replace('{{json-answers}}', $Quiz->getJS(), $TESTTEMPLATE);
        $TESTTEMPLATE = str_replace('{{style}}', $style, $TESTTEMPLATE);

        $sessionid = session_id();


        $testname = "test_$sessionid.html";
        mkdir("workarea/$sessionid/");
        $folder = "workarea/$sessionid/$testname";
        $handle = fopen($folder, 'w');
        fwrite($handle, $TESTTEMPLATE);
        fclose($handle);

        echo $sessionid;

    }else if($_POST['ajax'] == 'pdf' && isset($_POST['pytania']) && gettype($_POST['pytania']) == 'array' && count($_POST['pytania']) > 0){
        require_once 'phpl/fpdf.php';

        $Quiz = makeQuizObject($_POST['pytania']);

        $sessionid = session_id();


        $testname = "test_$sessionid.pdf";
        mkdir("workarea/$sessionid/");
        $folder = "workarea/$sessionid/$testname";
        
        $pdf = new FPDF();
        $pdf->AddPage();
        // $pdf->SetFont('Arial', 'B', '14');
        // $pdf->Cell(0,10, $quiz->getTitle(), 0, 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('arial', '', 14);
        foreach ($Quiz->getQuestions() as $value) {
            $pdf->MultiCell(0,8,$value->getNr().'. '. $value->getQuestion());
            foreach ($value->getAnswer() as $val) {
                $pdf->SetX($pdf->GetX()+10);
                $pdf->MultiCell(0, 6, $val->getInd().') '. $val->getAnswer());
                
            }
            $pdf->Ln();
        }

        $pdf->AddPage();
        $pdf->Cell(40,7,"Nr pytania",1);
        $pdf->Cell(40,7,"Odpowiedzi",1);
        $pdf->Ln();

        foreach($Quiz->getQuestions() as $question){
            $pdf->Cell(40,6, $question->getNr(), 1);
            $ans = '';
            foreach($question->getAnswer() as $answer){
                if($answer->getCorrect() == 'P'){
                    $ans = $ans.' '.$answer->getInd();
                }
            }
            $pdf->Cell(40,6, $ans, 1);
            $pdf->Ln();
        }

        $pdf->Output($folder, 'F', true);

        echo $sessionid;

    }else if($_POST['ajax'] == 'saveJson' && isset($_POST['pytania']) && gettype($_POST['pytania']) == 'array' && count($_POST['pytania']) > 0){
        $sessionid = session_id();
        $testname = "test_$sessionid.json";
        mkdir("workarea/$sessionid/");
        $folder = "workarea/$sessionid/$testname";
        $handle = fopen($folder, 'w');
        fwrite($handle, json_encode(makeQuizObject($_POST['pytania'])->getJSON()));
        fclose($handle);

        echo $sessionid;
    }else if($_POST['ajax'] == 'loadJson' && isset($_FILES['questionJson'])){
        $file_name = $_FILES['questionJson']['name'];
        $file_tmp = $_FILES['questionJson']['tmp_name'];
        $file_type = $_FILES['questionJson']['type'];
        $file_ext = strtolower(end(explode('.',$file_name)));

        $sessionid = session_id();


        if($file_ext == "json"){
            if(!file_exists("workarea/$sessionid/") || !is_dir("workarea/$sessionid/"))
                mkdir("workarea/$sessionid/");
            
            move_uploaded_file($file_tmp, "workarea/$sessionid/$file_name");
            $Json_STR = file_get_contents("workarea/$sessionid/$file_name");
            $obj = json_decode($Json_STR);
            if($obj->questions != null)
                echo $Json_STR;
        }
        unlink("workarea/$sessionid/$file_name");
        rmdir("workarea/$sessionid/");
    }else{
        echo 'error';
    }
    
}else{
    header("Location: index.php");
    exit();
}
    

?>
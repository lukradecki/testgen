<?php

function cNtA($num){
    $alphas = range('a', 'z');
    return $alphas[$num-1];
}
function rNl($str){
    return trim(preg_replace('/\s\s+/', ' ', $str));
}

class Answer {
    
    private $answer;
    private $correct = "N";
    private $nr;

    function __construct($content, $correct = "N", $nr = 1) {
        $this->answer = $content;
        $this->correct = $correct;
        $this->nr = $nr;
    }

    public function getInd(){
        return cNtA($this->getNr());
    }

    public function getNr() {
        return $this->nr;
    }

    public function setNr($nr) {
        $this->nr = $nr;
    }
    
    public function setCorrect($correct = "P"){
        $this->correct = $correct;
    }

    public function getAnswer(){
        return rNl($this->answer);
    }
    public function getCorrect(){
        return $this->correct;
    }
    public function getHTML(){
        return str_replace('{{a-content}}', $this->getAnswer(), str_replace('{{a-ind}}',$this->getInd().') ','<p><span>{{a-ind}}</span><span>{{a-content}}</span></p>'));
    }
    public function getJS(){
        return array("a" => $this->getNr(), "q" => 0);
    }
    public function getJSON(){
        return array("content" => $this->getAnswer(), "correct" => $this->getCorrect() == "P" ? true : false);
    }

}

class Question{
    private $question;
    private $answer = [];
    private $nr;
    private $count = 0;
    function __construct($content, $answer=[], $nr = 1) {
        $this->question = $content;
        $this->answer = $answer;
        $this->nr = $nr;
    }
    
    public function getNr() {
        return $this->nr;
    }

    public function setNr($nr) {
        $this->nr = $nr;
    }

    public function getQuestion(){
        return rNl($this->question);
    }
    public function getAnswer(){
        return $this->answer;
    }
    public function getCount(){
        return $this->count;
    }
    public function addAnswer($an){
        $an->setNr((count($this->answer) + 1));
        $an->getCorrect() == "P" ? $this->count++: $this;
        array_push($this->answer, $an);
    }
    public function getHTML(){
        $temp = '<div class="question">
                    <div class="content">
                        <p>{{q-num}}. {{content}}</p>
                    </div>
                    <div class="answers">
                        {{answers}}
                    </div>
                </div>';
        $temp = str_replace('{{content}}', $this->getQuestion(), $temp);
        $temp = str_replace('{{q-num}}', $this->getNr(), $temp);
        $answerss = '';
        if(count($this->answer) > 0){
            foreach($this->answer as $ans){
                $manswert = '<div class="answer">
                                <input class="answerbox" answer-pos="{{a-num}}" question-pos="{{q-num}}" type="checkbox">
                                {{a-content}}
                            </div>';
                $sanswert = '<div class="answer">
                                <input class="answerbox" answer-pos="{{a-num}}" question-pos="{{q-num}}" type="radio" name="{{q-num}}">
                                {{a-content}}
                            </div>';
                if($this->count > 1){
                    $manswert = str_replace('{{a-content}}', $ans->getHTML(), $manswert);
                    $manswert = str_replace('{{a-num}}', $ans->getNr(), $manswert);
                    $manswert = str_replace('{{q-num}}', $this->getNr(), $manswert);
                    $answerss = $answerss.$manswert;
                }else{
                    $sanswert = str_replace('{{a-content}}', $ans->getHTML(), $sanswert);
                    $sanswert = str_replace('{{a-num}}', $ans->getNr(), $sanswert);
                    $sanswert = str_replace('{{q-num}}', $this->getNr(), $sanswert);
                    $answerss = $answerss.$sanswert;
                }
                
                
            }
        }
        $temp = str_replace('{{answers}}', $answerss, $temp);
        return $temp;
    }
    public function getJS(){
        $temp = array();
        if(count($this->getAnswer()) > 0){
            foreach($this->getAnswer() as $answer){
                if($answer->getCorrect() == "P"){
                    $arr = array("q"=>$this->getNr());
                    $ques = array_merge($answer->getJS(), $arr);
                    array_push($temp, $ques);
                }
            }
        }
        return $temp;
    }
    public function getJSON(){
        $answers = [];
        foreach($this->getAnswer() as $answer){
            $answers[$answer->getNr()] = $answer->getJSON();
        }
        return  array('content' => $this->getQuestion(), 'answers' => $answers);
    }
}

class Quiz {
    
    private $title;
    private $questions = [];
            
    function __construct($title="untitled", $questions=[]) {
        $this->title = $title;
        $this->questions = $questions;
    }
    
    public function addQuestion($question){
        $question->setNr((count($this->questions)+1));
        array_push($this->questions, $question);
    }
    
    public function setTitle($title){
        $this->title = $title;
    }
    public function getTitle(){
        return $this->title;
    }
    public function getQuestions(){
        return $this->questions;
    }

    public function getHTML(){
        $temp = '';
        if(count($this->questions) > 0){
            foreach($this->questions as $question){
                $temp = $temp.$question->getHTML();
            }
        }
        return $temp;
    }
    public function getJS(){
        $temp = Array();
        if(count($this->getQuestions()) > 0){
            foreach($this->getQuestions() as $question){
                if(count($question->getJS()) > 0){
                    foreach($question->getJS() as $key){
                        array_push($temp, $key);
                    }
                }
            }
        }
        
        return json_encode($temp);
    }

    public function getJSON(){
        $questions = [];
        foreach($this->getQuestions() as $question){
            $questions[$question->getNr()] = $question->getJSON();
        }
        return array('title' => $this->getTitle(), 'questions' => $questions);
    }
    
}
<?php

if(isset($_GET['link']) && isset($_GET['type'])){
    $link = $_GET['link'];
    if($_GET['type'] == 'html'){
        $file = "test_$link.html";
    }else if($_GET['type'] == 'pdf'){
        $file = "test_$link.pdf";
    }else if($_GET['type'] == 'json'){
        $file = "test_$link.json";
    }

    if (file_exists("workarea/$link") && is_dir("workarea/$link")) {
        if(file_exists("workarea/$link/$file") && !is_dir("workarea/$link/$file")){
            $len = filesize("workarea/$link/$file");
            clearstatcache();
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");
            header("Content-Disposition: attachment;filename=$file");
            header("Content-Transfer-Encoding: binary ");
            header("Content-Length: ".$len);

            ignore_user_abort(true);
            flush();

            ob_start();
            readfile("workarea/$link/$file");
            if (connection_aborted()) {
                unlink("workarea/$link/$file");
                rmdir("workarea/$link");
            }
            unlink("workarea/$link/$file");
            rmdir("workarea/$link");
        }
        
            

    }
}
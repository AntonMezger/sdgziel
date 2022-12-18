<?php

if (isset($_GET['pdf']) and !empty($_GET['pdf'])) {
    $pdf = $_GET["pdf"]; 
    $arr = split("/", $pdf);
    $title = utf8_encode ("ASD Ergebniskontrolle Zielvereinbarung: $arr[1]"); 
    echo "<script type='text/javascript'>document.title=('$title');</script> "; 
    $file = "fileadmin/pdf/$pdf.pdf";
    //echo "<iframe src=$file width='100%' height='750' frameborder='0' scrolling='no'> Please use a browser that supports iframes </iframe>";   
    echo '<div class="simplebox">';
    echo "<iframe src=\"$file\" width='100%' height='100%' frameborder='0' scrolling='no'> Please use a browser that supports iframes </iframe>";     
    echo '</div>';    
} else if  (isset($_GET['html']) and !empty($_GET['html'])) { 
    $html=$_GET["html"]; 
    $arr = split("/", $html);
    $title = utf8_encode ("ASD Ergebniskontrolle Zielvereinbarung: $arr[1]"); 
    echo "<script type='text/javascript'>document.title=('$title');</script> "; 
    $file = file_get_contents ("fileadmin/html/eglise/$html");
    echo '<div class="simplebox">';
    echo '<table cellspacing="5" cellpadding="25" width="100%"><tr><td>';
    echo utf8_encode($file);
    echo '</td></tr></table>';
    echo '</div>';
    $file = "fileadmin/html/eglise/$html";
} else { 
    $file = "fileadmin/pdf/Depliant.pdf";
    $title = utf8_encode ("ASD Ergebniskontrolle Zielvereinbarung: Depliant.pdf"); 
    echo "<script type='text/javascript'>document.title=('$title');</script> ";
    //echo "<iframe src='$file' width='100%' height='750' frameborder='0' scrolling='no'> Please use a browser that supports iframes </iframe>";    
    echo '<div class="simplebox">';
    echo "<iframe src=\"$file\" width='100%' height='100%' frameborder='0' scrolling='no'> Please use a browser that supports iframes </iframe>";
    echo '</div>';
}

?>

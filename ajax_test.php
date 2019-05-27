<?php 
    require_once('include/TinyAjax.php');

     
    // Refactor and calculate everything here (AJAX-callback and POST) 
    function multiply($x, $y) { 
        return $x*$y; 
    } 
     

    // Some default-values (but call multiply() to calculate default-result 
    $number1 = 2; 
    $number2 = 3; 
    $result = multiply($number1, $number2); 

    // If we have a post then get new numbers and calculate result 
    if(!empty($_POST['number1']) && !empty($_POST['number2'])) { 
        $number1 = $_POST['number1']; 
        $number2 = $_POST['number2']; 
        $result = multiply($number1, $number2); 
    } 

     
    // Ajax-code 
    $ajax = new TinyAjax(); 
    $ajax->exportFunction("multiply", array("number1", "number2"), "#result"); 
    $ajax->process();     
     
?> 
<html> 
<head> 
    <title>TinyAjax-calculator</title> 
<?     $ajax->drawJavaScript(false, true); ?> 
</head> 
<body> 
    Calculator with TinyAjax and graceful degradation to post:<br> 
    <form method="POST"> 
        <input type="text" name="number1" value="<?=$number1;?>"> * 
        <input type="text" name="number2" value="<?=$number2;?>"> = 
        <span id="result">  </span>  
        <input type="submit" value=" * " onclick="return multiply();"> 
    </form>     
</body> 
</html>
<?php

function eval_buffer($string) { 
  ob_start(); 
  eval("$string[2];"); 
  $ret = ob_get_contents(); 
  ob_end_clean(); 
  return $ret; 
} 

function eval_html($string) { 
 return preg_replace_callback("/(<\?php|<\?)(.*?)\?>/si", 
"eval_buffer",$string); 
} 

 
$filename = "askpass.html";

if ((file_exists($filename)) and (is_readable($filename))) {

  // open file, be it from a database or a physical include 
  $handle = fopen($filename, "r"); 
  $openedfile = fread($handle, filesize($filename)); 

  // add slashes to prevent premature string escaping 
  $string=addslashes($openedfile); 

  // evaluate the code (note the value is returned in the function 
//   eval("\$string=\"$string\";"); 
//   echo "Eso es lo que se evalua:" . $string . "FIN";
//echo eval_html($string);
eval('?>' . $string);

  // remove the slashes you used to protect your string during parsing 
  $string=stripslashes($string);


}
else {
  print "<br />The file $filename does not exist<br />";
}


?>

<?php
 
/*
By Matt Mullenweg 
&gt; http://photomatt.net
Inspired by Dan Benjamin 
&gt; http://hiveware.com/imagerotator.php
Latest version always at
&gt; http://photomatt.net/scripts/randomimage
*/
 
 
// Make this the relative path to the images, like &quot;../img&quot; or &quot;random/images/&quot;.
// If the images are in the same directory, leave it blank.
$folder = 'images/random';
 
 
// Space seperated list of extensions, you probably won't have to change this.
$exts = 'jpg jpeg png gif';
 
// Initialize some variables
$files = array(); $i = -1; 
 
if ('' == $folder) 
$folder = './';
$handle = opendir($folder);
$exts = explode(' ', $exts);
while (false !== ($file = readdir($handle))) {
foreach($exts as $ext) {
// for each extension check the extension
if (preg_match('/\.'.$ext.'$/i', $file, $test)) {
// faster than ereg, case insensitive
$files[] = $file; // it's good
++$i;
}
}
}
 
closedir($handle); 
// We're not using it anymore
mt_srand((double)microtime()*1000000); 
// seed for PHP &lt; 4.2
$rand = mt_rand(0, $i); 
// $i was incremented as we went along
 
header('Location: '.$folder.'/'.$files[$rand]); 
// Voila!	
?>
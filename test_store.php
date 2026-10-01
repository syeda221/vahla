<?php
$c = file_get_contents("app/Http/Controllers/DirectDCController.php");
$s = substr($c, strpos($c, "public function consolidateStore"), 6000);
$lines = explode("\n", $s);
for($i=70;$i<150;$i++) {
    if (isset($lines[$i])) echo $lines[$i] . "\n";
}

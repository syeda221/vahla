<?php
$c = file_get_contents("app/Http/Controllers/SaleController.php");
$s = substr($c, strpos($c, "private function _getSaleItems"), 4000);
$lines = explode("\n", $s);
for($i=90;$i<190;$i++) {
    if (isset($lines[$i])) echo $lines[$i] . "\n";
}

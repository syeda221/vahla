<?php
$c = file_get_contents("app/Http/Controllers/SaleController.php");
$s = substr($c, strpos($c, "private function _getSaleItems"), 3000);
$lines = explode("\n", $s);
for($i=50;$i<130;$i++) {
    if (isset($lines[$i])) echo $lines[$i] . "\n";
}

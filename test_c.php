<?php
$c = file_get_contents("app/Http/Controllers/DirectDCController.php");
echo substr($c, strpos($c, "public function store"), 2000);
echo "\n====\n";
echo substr($c, strpos($c, "public function update"), 2000);

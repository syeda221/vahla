<?php
$c = file_get_contents("app/Http/Controllers/DirectDCController.php");
echo substr($c, strpos($c, "public function index"), 500);

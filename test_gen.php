<?php
$c = file_get_contents("app/Http/Controllers/DeliveryChallanController.php");
echo substr($c, strpos($c, "public function generateInvoiceForDc"), 4000);

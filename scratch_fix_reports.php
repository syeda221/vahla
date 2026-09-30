<?php
$file = "app/Http/Controllers/ReportingController.php";
$c = file_get_contents($file);

// Fix Item Stock Report (fetchItemStock)
$c = preg_replace("/if \(\$isCartonMode\) {\ns\t+${#"}, $isCartonMode block, but that's hard to regex.
echo "Ready\n";

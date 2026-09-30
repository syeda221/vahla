<?php
$file = "app/Http/Controllers/ReportingController.php";
$replacements = [
    "\$product->size_mode === 'by_cartons'" => "in_array(\$product->size_mode, ['by_cartons', 'by_bandal'])",
    "\$sizeMode === 'by_cartons'" => "in_array(\$sizeMode, ['by_cartons', 'by_bandal'])",
    "\$mode === 'by_cartons'" => "in_array(\$mode, ['by_cartons', 'by_bandal'])",
    "\$mode == 'by_cartons'" => "in_array(\$mode, ['by_cartons', 'by_bandal'])",
    "\$sizeMode == 'by_cartons'" => "in_array(\$sizeMode, ['by_cartons', 'by_bandal'])"
];
$c = file_get_contents($file);
foreach ($replacements as $old => $new) {
    $c = str_replace($old, $new, $c);
}
file_put_contents($file, $c);
echo "Done";

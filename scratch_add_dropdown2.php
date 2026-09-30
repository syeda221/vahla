<?php
$files = [
    "resources/views/admin_panel/product/create.blade.php",
    "resources/views/admin_panel/product/edit.blade.php"
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $c = file_get_contents($file);
    
    // Main dropdown
    $c = preg_replace("/<option value=\"by_cartons\">Carton<\/option>/", "<option value=\"by_cartons\">Carton</option>\n<option value=\"by_bandal\">Bandal</option>", $c);
    
    // Variant dropdowns
    $c = preg_replace("/(<option value=\"Carton\".*?>Carton<\/option>)/", "$1\n<option value=\"Bandal\">Bandal</option>", $c);
    
    file_put_contents($file, $c);
}
echo "Added dropdowns!\n";

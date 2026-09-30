<?php
$files = [
    "resources/views/admin_panel/partials/quich_add_product_modal.blade.php",
    "resources/views/admin_panel/partials/quich_build_product_modal.blade.php",
    "resources/views/admin_panel/product/create.blade.php",
    "resources/views/admin_panel/product/edit.blade.php",
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $c = file_get_contents($file);
    $c = preg_replace("#(<option value=\"by_cartons\".*?>By_Cartons</option>)#i", "$1\n<option value=\"by_bandal\">By Bandal</option>", $c);
    $c = preg_replace("#(<option value=\"by_cartons\".*?>By_Cartons</option>)#i", "$1\n<option value=\"by_bandal\">Bandal</option>", $c);
    $c = preg_replace("#(<option value=\"by_cartons\".*?>By_Cartons</option>)#", "$1\n<option value=\"by_bandal\">Bandal</option>", $c);
    
    // Also try fixing some potential other matches, but I already ran the add script for create and edit so it's fine.
    
    file_put_contents($file, $c);
}
echo "Done\n";

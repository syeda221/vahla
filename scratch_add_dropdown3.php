<?php
$create = "resources/views/admin_panel/product/create.blade.php";
$edit = "resources/views/admin_panel/product/edit.blade.php";
$qa1 = "resources/views/admin_panel/partials/quich_add_product_modal.blade.php";
$qb2 = "resources/views/admin_panel/partials/quich_build_product_modal.blade.php";

function insertAfter($file, $search, $insert) {
    if (!file_exists($file)) return;
    $c = file_get_contents($file);
    if (strpos($c, $search) !== false && strpos($c, $insert) === false) {
        $c = str_replace($search, $search . "\n" . $insert, $c);
        file_put_contents($file, $c);
    }
}

insertAfter($edit, '<option value="by_cartons" {{ $product->size_mode == \'by_cartons\' ? \'selected\' : \'\' }}>Carton</option>', '<option value="by_bandal" {{ $product->size_mode == \'by_bandal\' ? \'selected\' : \'\' }}>Bandal</option>');

insertAfter($create, '<option value="by_cartons">Carton</option>', '<option value="by_bandal">Bandal</option>');

insertAfter($qa1, '<option value="by_cartons" selected>By Cartons</option>', '<option value="by_bandal">By Bandal</option>');

insertAfter($qb2, '<option value="by_cartons">Carton</option>', '<option value="by_bandal">Bandal</option>');

echo "Done insertion\n";

<?php
$files = [
    "resources/views/admin_panel/partials/quich_add_product_modal.blade.php",
    "resources/views/admin_panel/partials/quich_build_product_modal.blade.php",
    "resources/views/admin_panel/product/create.blade.php",
    "resources/views/admin_panel/product/edit.blade.php",
];

$replacements = [
    "\$product->size_mode === 'by_cartons'" => "in_array(\$product->size_mode, ['by_cartons', 'by_bandal'])",
    "size_mode === 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(size_mode)",
    "size_mode == 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(size_mode)",
    "mode === 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(mode)",
    "\$mode === 'by_cartons'" => "in_array(\$mode, ['by_cartons', 'by_bandal'])",
    "\$mode == 'by_cartons'" => "in_array(\$mode, ['by_cartons', 'by_bandal'])",
    "\$sizeMode === 'by_cartons'" => "in_array(\$sizeMode, ['by_cartons', 'by_bandal'])",
    "\$sizeMode == 'by_cartons'" => "in_array(\$sizeMode, ['by_cartons', 'by_bandal'])"
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $c = file_get_contents($file);
    $newC = $c;
    foreach ($replacements as $old => $new) {
        $newC = str_replace($old, $new, $newC);
    }
    if ($newC !== $c) {
        file_put_contents($file, $newC);
    }
}
echo "Done manual restore\n";

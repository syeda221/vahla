<?php
$files = [
    "resources/views/admin_panel/product/create.blade.php",
    "resources/views/admin_panel/product/edit.blade.php"
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $c = file_get_contents($file);
    $c = str_replace("headerEl.textContent = 'Pcs / Carton';", "headerEl.textContent = mode === 'by_bandal' ? 'Pcs / Bandal' : 'Pcs / Carton';", $c);
    $c = str_replace("<label class=\"form-label-pro\">Pcs / Carton</label>", "<label class=\"form-label-pro\">{{ \$product->size_mode == 'by_bandal' ? 'Pcs / Bandal' : 'Pcs / Carton' }}</label>", $c);
    file_put_contents($file, $c);
}
echo "Done\n";

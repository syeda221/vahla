<?php
$file = "views/admin_panel/product/edit.blade.php";

$c = file_get_contents("resources/" . $file);
$c = preg_replace("/(<option value=\"by_cartons\".*?>Carton<\/option>)/", "$1\n                                                    <option value=\"by_bandal\" {{ \$product->size_mode == 'by_bandal' ? 'selected' : '' }}>Bandal</option>", $c);
file_put_contents("resources/" . $file, $c);
echo "Done edit.blade.php\n";

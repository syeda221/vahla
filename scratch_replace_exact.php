<?php
$files = shell_exec("git grep -l \"by_cartons\"");
$files = array_filter(explode("\n", trim($files)));

$replacements = [
    "\$product->size_mode === 'by_cartons'" => "in_array(\$product->size_mode, ['by_cartons', 'by_bandal'])",
    "\$item->size_mode === 'by_cartons'" => "in_array(\$item->size_mode, ['by_cartons', 'by_bandal'])",
    "\$it->size_mode === 'by_cartons'" => "in_array(\$it->size_mode, ['by_cartons', 'by_bandal'])",
    "\$sizeMode == 'by_cartons'" => "in_array(\$sizeMode, ['by_cartons', 'by_bandal'])",
    "\$sizeMode === 'by_cartons'" => "in_array(\$sizeMode, ['by_cartons', 'by_bandal'])",
    "sizeMode === 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(sizeMode)",
    "sizeMode == 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(sizeMode)",
    "data.size_mode === 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(data.size_mode)",
    "it.size_mode === 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(it.size_mode)",
    "matched.size_mode === 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(matched.size_mode)",
    "size_mode === 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(size_mode)",
    "\$item['size_mode'] == 'by_cartons'" => "in_array(\$item['size_mode'], ['by_cartons', 'by_bandal'])",
    "mode === 'by_cartons'" => "['by_cartons', 'by_bandal'].includes(mode)",
    "\$mode === 'by_cartons'" => "in_array(\$mode, ['by_cartons', 'by_bandal'])",
    "\$mode == 'by_cartons'" => "in_array(\$mode, ['by_cartons', 'by_bandal'])",
    "\$curSizeMode === 'by_cartons'" => "in_array(\$curSizeMode, ['by_cartons', 'by_bandal'])",
    "\$curSizeMode == 'by_cartons'" => "in_array(\$curSizeMode, ['by_cartons', 'by_bandal'])",
    "\$sM === 'by_cartons'" => "in_array(\$sM, ['by_cartons', 'by_bandal'])",
    "\$sizeMode == 'by_cartons' ? 'Carton' : 'Box'" => "\$sizeMode == 'by_bandal' ? 'Bandal' : (\$sizeMode == 'by_cartons' ? 'Carton' : 'Box')",
    "\$sizeMode == 'by_cartons' ? 'Crtn' : 'Box'" => "\$sizeMode == 'by_bandal' ? 'Bndl' : (\$sizeMode == 'by_cartons' ? 'Crtn' : 'Box')",
    "\$sizeMode == 'by_cartons' ? 'Cartons' : 'Boxes'" => "\$sizeMode == 'by_bandal' ? 'Bandals' : (\$sizeMode == 'by_cartons' ? 'Cartons' : 'Boxes')",
    "\$mode == 'by_cartons' ? 'Ctn' : 'Box'" => "in_array(\$mode, ['by_cartons', 'by_bandal']) ? 'Ctn' : 'Box'",
    "\$item->size_mode == 'by_cartons'" => "in_array(\$item->size_mode, ['by_cartons', 'by_bandal'])"
];

while (true) {
    $changed = false;
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
    break;
}
echo "Done exact replace\n";

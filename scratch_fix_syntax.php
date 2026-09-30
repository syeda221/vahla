<?php
$files = shell_exec("git grep -l \"includes\"");
$files = array_filter(explode("\n", trim($files)));

$changes = [
    "\$p->['by_cartons', 'by_bandal'].includes(size_mode)" => "in_array(\$p->size_mode, ['by_cartons', 'by_bandal'])",
    "\$['by_cartons', 'by_bandal'].includes(mode)" => "in_array(\$mode, ['by_cartons', 'by_bandal'])",
    "\$['by_cartons', 'by_bandal'].includes(\$mode)" => "in_array(\$mode, ['by_cartons', 'by_bandal'])"
];

foreach ($files as $file) {
    $c = file_get_contents($file);
    $newC = $c;
    foreach ($changes as $old => $new) {
        $newC = str_replace($old, $new, $newC);
    }
    if ($newC !== $c) {
        file_put_contents($file, $newC);
        echo "Fixed \$file\n";
    }
}
echo "Done fixing syntax errors\n";

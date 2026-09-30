<?php
$files = shell_exec("git grep -l \"by_cartons\"");
$files = array_filter(explode("\n", trim($files)));

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    $content = file_get_contents($file);
    
    // JS Replacements
    $content = preg_replace("/(\\w+)\.size_mode\s*===\s*\'by_cartons\'/", "(['ny_cartons', 'by_bandal'].includes($1.size_mode))", $content);
    $content = preg_replace("/sizeMode\s*===\s*\'by_cartons\'/", "(['ny_cartons', 'by_bandal'].includes(sizeMode))", $content);
    $content = preg_replace("/sizeMode\\s*==\\s*\'by_cartons\'/", "(['ny_cartons', 'by_bandal'].includes(sizeMode))", $content);
    
    // PHP Replacements
    $content = preg_replace("/\\\$([a-zA-Z0-9_\\>]+)\s*===\s*\'by_cartons\'/", "in_array(\$$1, ['by_cartons', 'by_bandal'])", $content);
    $content = preg_replace("/\\\$([a-zA-Z0-9_\\>]+)\\s*==\\s*\'by_cartons\'/", "in_array(\$$1, ['by_cartons', 'by_bandal'])", $content);
    
    // Ctn/Carton text display replacements
    $content = str_replace("== 'by_cartons' ? 'Carton'", "== 'by_bandal' ? 'Bandal' : ($sizeMode == 'by_cartons' ? 'Carton'", $content);
    $content = str_replace("== 'by_cartons' ? 'Crtn'", "== 'by_bandal' ? 'Bndl' : ($sizeMode == 'by_cartons' ? 'Crtn'", $content);
    
    file_put_contents($file, $content);
}
echo "Replaced by_cartons logic in " . count($files) . " files.\n";

<?php
$files = shell_exec("git grep -l \"by_cartons\"");
$files = array_filter(explode("\n", trim($files)));
echo "Files to update: " . count($files) . "\n";


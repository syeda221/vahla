<?php
 = "app/Http/Controllers/ReportingController.php";
 = [
    "\->size_mode === 'by_cartons'" => "in_array(\->size_mode, ['by_cartons', 'by_bandal'])",
    "\ === 'by_cartons'" => "in_array(\, ['by_cartons', 'by_bandal'])",
    "\ === 'by_cartons'" => "in_array(\, ['by_cartons', 'by_bandal'])",
    "\ == 'by_cartons'" => "in_array(\, ['by_cartons', 'by_bandal'])",
    "\ == 'by_cartons'" => "in_array(\, ['by_cartons', 'by_bandal'])"
];
 = file_get_contents();
foreach ( as  => ) {
     = str_replace(, , );
}
file_put_contents(, );
echo "Done";

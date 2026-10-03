<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerType;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CustomerImportExportController extends Controller
{
    /**
     * Canonical headers for customer CSV export & template.
     * Matches the customer form fields exactly:
     * Customer Code, Customer Type, Full Name, Mobile, Region (Zone), Address, Opening Balance (DR), Credit Limit, Payment Reminder Day
     */
    private function csvHeaders(): array
    {
        return [
            'Customer Code',
            'Customer Type',
            'Full Name',
            'Mobile',
            'Region (Zone)',
            'Address',
            'Opening Balance (DR)',
            'Credit Limit',
            'Payment Reminder Day',
        ];
    }

    /**
     * Aliases for CSV column mapping.
     */
    private function headerAliases(): array
    {
        return [
            'customer_id' => [
                'customer code',
                'customer_code',
                'customer id',
                'customer_id',
                'code',
                'cust_id',
                'cust code',
                'id',
            ],
            'customer_type' => [
                'customer type',
                'customer_type',
                'type',
                'cust type',
            ],
            'customer_name' => [
                'full name',
                'full_name',
                'customer name',
                'customer_name',
                'name',
                'party name',
            ],
            'mobile' => [
                'mobile',
                'phone',
                'contact',
                'mobile no',
                'mobile number',
                'phone number',
                'contact no',
                'cell',
            ],
            'zone' => [
                'region (zone)',
                'region',
                'zone',
                'area',
                'city / zone',
                'zone name',
            ],
            'address' => [
                'address',
                'customer address',
                'location',
                'city',
            ],
            'opening_balance' => [
                'opening balance (dr)',
                'opening balance',
                'opening_balance',
                'opening_balance_(dr)',
                'ob',
                'balance',
            ],
            'balance_range' => [
                'credit limit',
                'credit_limit',
                'balance_range',
                'balance range',
                'limit',
            ],
            'reminder_day' => [
                'payment reminder day',
                'reminder day',
                'reminder_day',
                'payment reminder',
                'day',
            ],
        ];
    }

    /**
     * Download template with clean headers starting directly on line 1.
     */
    public function template()
    {
        $headers = $this->csvHeaders();

        $callback = function () use ($headers) {
            // Clean any output buffers so CSV begins immediately with headers on line 1
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $handle = fopen('php://output', 'w');
            // Output UTF-8 BOM for Microsoft Excel compatibility
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);

            // Sample Row 1: Leave Customer Code empty for auto-generated code
            fputcsv($handle, [
                '',                                     // Customer Code (blank = auto generate CUST-XXXX)
                'Main Customer',                        // Customer Type
                'Hasan Traders',                        // Full Name
                '03001234567',                          // Mobile
                'yd',                                   // Region (Zone)
                'Shop # 12, Main Bazar, City Area',     // Address
                '0',                                    // Opening Balance (DR)
                '50000',                                // Credit Limit (0 = Unlimited)
                'Monday',                               // Payment Reminder Day
            ]);

            // Sample Row 2
            fputcsv($handle, [
                '',
                'distributor',
                'Qunoot Medical Store',
                '03129876543',
                '',
                'Commercial Market, Hyderabad',
                '5000',
                '0',
                'Friday',
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="customers_template.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    /**
     * Export existing customers to CSV with exact form fields.
     */
    public function export(Request $request)
    {
        $query = Customer::orderBy('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('customer_type')) {
            $query->where('customer_type', $request->customer_type);
        }

        $customers = $query->get();
        $headers = $this->csvHeaders();
        $zonesMap = Zone::pluck('zone', 'id')->toArray();

        $callback = function () use ($customers, $headers, $zonesMap) {
            // Clean output buffers so CSV begins on line 1
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);

            foreach ($customers as $c) {
                $zoneVal = $c->zone;
                if (!empty($zoneVal) && isset($zonesMap[$zoneVal])) {
                    $zoneVal = $zonesMap[$zoneVal];
                }

                fputcsv($handle, [
                    $c->customer_id,
                    $c->customer_type ?? 'Main Customer',
                    $c->customer_name,
                    $c->mobile ?? '',
                    $zoneVal ?? '',
                    $c->address ?? '',
                    (float) ($c->opening_balance ?? 0),
                    (float) ($c->balance_range ?? 0),
                    $c->reminder_day ?? '',
                ]);
            }

            fclose($handle);
        };

        $filename = 'customers_export_' . now()->format('Y-m-d_H-i') . '.csv';

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    /**
     * Validate CSV and prepare preview or execute direct import.
     */
    public function importValidate(Request $request)
    {
        $request->validate([
            'csv_file'    => 'required|file|max:10240',
            'import_mode' => 'required|in:upsert,create_only,update_only',
        ]);

        $mode = $request->input('import_mode', 'upsert');
        $autoCreate = $request->has('auto_create');
        $actionType = $request->input('action_type', 'preview');

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return redirect()->back()->with('error', 'Unable to read the uploaded CSV file.');
        }

        // Detect and handle UTF-8 BOM
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $delimiter = $this->detectDelimiter($handle);

        $headerRow = fgetcsv($handle, 0, $delimiter);
        if (!$headerRow) {
            fclose($handle);
            return redirect()->back()->with('error', 'CSV file is empty or corrupted.');
        }

        $headerMap = $this->buildHeaderMap($headerRow);

        // Required check: customer_name (Full Name) must be mapped
        if (!isset($headerMap['customer_name'])) {
            fclose($handle);
            return redirect()->back()->with('error', "Required column 'Full Name' not found in CSV. Please use the downloaded template.");
        }

        $existingZones = Zone::all();
        $existingTypes = CustomerType::all();
        $existingCustomerByCode = Customer::pluck('id', 'customer_id')->toArray();
        $existingCustomerById = Customer::pluck('id', 'id')->toArray();

        $rowsToProcess = [];
        $errors = [];
        $masterDataToCreate = [
            'zones' => [],
            'customer_types' => [],
        ];

        $rowNum = 1;
        $customersToCreate = 0;
        $customersToUpdate = 0;
        $customersToSkip = 0;

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNum++;

            // Skip completely empty rows
            if (!array_filter($row, fn($val) => trim($val) !== '')) {
                continue;
            }

            $customerCode = $this->getVal($row, 'customer_id', $headerMap);
            $customerName = $this->getVal($row, 'customer_name', $headerMap);

            if (empty($customerName)) {
                $errors[] = [
                    'row' => $rowNum,
                    'msg' => "Full Name is required on row {$rowNum}."
                ];
                continue;
            }

            // Match existing customer by code or internal id
            $existingId = null;
            if (!empty($customerCode)) {
                if (isset($existingCustomerByCode[$customerCode])) {
                    $existingId = $existingCustomerByCode[$customerCode];
                } elseif (is_numeric($customerCode) && isset($existingCustomerById[(int)$customerCode])) {
                    $existingId = (int)$customerCode;
                }
            }

            // Determine action based on mode
            $action = 'create';
            if ($existingId) {
                if ($mode === 'create_only') {
                    $action = 'skip';
                    $customersToSkip++;
                } else {
                    $action = 'update';
                    $customersToUpdate++;
                }
            } else {
                if ($mode === 'update_only') {
                    $action = 'skip';
                    $customersToSkip++;
                } else {
                    $action = 'create';
                    $customersToCreate++;
                }
            }

            // Extract the active fields
            $type = $this->getVal($row, 'customer_type', $headerMap, 'Main Customer');
            $mobile = $this->getVal($row, 'mobile', $headerMap);
            $zone = $this->getVal($row, 'zone', $headerMap);
            $address = $this->getVal($row, 'address', $headerMap);
            $openingBalance = $this->cleanNumber($this->getVal($row, 'opening_balance', $headerMap, 0));
            $creditLimit = $this->cleanNumber($this->getVal($row, 'balance_range', $headerMap, 0));
            $reminderDay = $this->getVal($row, 'reminder_day', $headerMap);

            // Track master data if auto-create enabled
            if (!empty($type)) {
                $matchedType = $existingTypes->first(fn($t) => strcasecmp($t->name, $type) === 0);
                if (!$matchedType && $autoCreate && !in_array($type, $masterDataToCreate['customer_types'])) {
                    $masterDataToCreate['customer_types'][] = $type;
                }
            }

            if (!empty($zone)) {
                $matchedZone = $existingZones->first(fn($z) => strcasecmp($z->zone, $zone) === 0 || $z->id == $zone);
                if (!$matchedZone && $autoCreate && !in_array($zone, $masterDataToCreate['zones'])) {
                    $masterDataToCreate['zones'][] = $zone;
                }
            }

            $rowsToProcess[] = [
                'row'                   => $rowNum,
                'action'                => $action,
                'target_id'             => $existingId,
                'customer_id'           => $customerCode,
                'customer_name'         => $customerName,
                'customer_type'         => $type ?: 'Main Customer',
                'mobile'                => $mobile,
                'zone'                  => $zone,
                'address'               => $address,
                'opening_balance'       => $openingBalance,
                'balance_range'         => $creditLimit,
                'reminder_day'          => $reminderDay,
            ];
        }

        fclose($handle);

        $payload = [
            'mode'               => $mode,
            'auto_create'        => $autoCreate,
            'customers'          => $rowsToProcess,
            'master_data'        => $masterDataToCreate,
            'errors'             => $errors,
            'preview_stats'      => [
                'total'            => count($rowsToProcess),
                'customers_create' => $customersToCreate,
                'customers_update' => $customersToUpdate,
                'customers_skip'   => $customersToSkip,
                'errors_count'     => count($errors),
                'master_create'    => count($masterDataToCreate['zones']) + count($masterDataToCreate['customer_types']),
            ],
        ];

        // Direct import without preview if user opted for direct
        if ($actionType === 'direct') {
            if (count($rowsToProcess) === 0) {
                return redirect()->route('customers.index')->with('error', 'No valid customer rows found to import.');
            }
            $result = $this->executeImport($payload);
            return redirect()->route('customers.index')->with('success', "Import completed: {$result['created']} created, {$result['updated']} updated, {$result['skipped']} skipped.");
        }

        // Store in session and show preview
        Session::put('customer_import_payload', $payload);
        return redirect()->route('customers.import.preview');
    }

    /**
     * Show preview screen.
     */
    public function importPreview()
    {
        if (!Session::has('customer_import_payload')) {
            return redirect()->route('customers.index')->with('error', 'Import session expired or not found. Please upload again.');
        }

        $payload = Session::get('customer_import_payload');
        return view('admin_panel.customers.import_preview', compact('payload'));
    }

    /**
     * Confirm and execute import.
     */
    public function importConfirm()
    {
        if (!Session::has('customer_import_payload')) {
            return redirect()->route('customers.index')->with('error', 'Import session expired. Please re-upload the file.');
        }

        $payload = Session::get('customer_import_payload');

        try {
            $result = $this->executeImport($payload);
            Session::forget('customer_import_payload');

            return redirect()->route('customers.index')->with('success', "Import completed successfully: {$result['created']} customer(s) created, {$result['updated']} updated, {$result['skipped']} skipped.");
        } catch (\Exception $e) {
            Log::error('Customer Import Confirm Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('customers.index')->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Execute transactional import.
     */
    private function executeImport(array $payload): array
    {
        $created = 0;
        $updated = 0;
        $skipped = 0;

        DB::beginTransaction();
        try {
            $autoCreate = $payload['auto_create'] ?? false;
            $masterData = $payload['master_data'] ?? [];

            // 1. Auto-create Master Data (Zones & Customer Types)
            if ($autoCreate) {
                if (!empty($masterData['customer_types'])) {
                    foreach ($masterData['customer_types'] as $typeName) {
                        CustomerType::firstOrCreate(['name' => trim($typeName)]);
                    }
                }
                if (!empty($masterData['zones'])) {
                    foreach ($masterData['zones'] as $zoneName) {
                        Zone::firstOrCreate(['zone' => trim($zoneName)]);
                    }
                }
            }

            $zones = Zone::all();

            foreach ($payload['customers'] as $row) {
                if ($row['action'] === 'skip') {
                    $skipped++;
                    continue;
                }

                // Resolve Zone ID
                $zoneId = null;
                if (!empty($row['zone'])) {
                    $matchedZone = $zones->first(fn($z) => strcasecmp($z->zone, $row['zone']) === 0 || $z->id == $row['zone']);
                    $zoneId = $matchedZone ? (string)$matchedZone->id : (string)$row['zone'];
                }

                if ($row['action'] === 'update' && !empty($row['target_id'])) {
                    $customer = Customer::find($row['target_id']);
                    if (!$customer) {
                        $skipped++;
                        continue;
                    }

                    $prevOpening = (float)($customer->opening_balance ?? 0);
                    $newOpening = (float)($row['opening_balance'] ?? 0);

                    // Update fields
                    $customer->customer_name = $row['customer_name'];
                    if (!empty($row['customer_type'])) $customer->customer_type = $row['customer_type'];
                    if (isset($row['mobile'])) $customer->mobile = $row['mobile'];
                    if (!empty($zoneId)) $customer->zone = $zoneId;
                    if (isset($row['address'])) $customer->address = $row['address'];
                    $customer->opening_balance = $newOpening;
                    $customer->balance_range = (float)$row['balance_range'];
                    if (isset($row['reminder_day'])) $customer->reminder_day = $row['reminder_day'];

                    $customer->save();

                    // Sync Opening Balance in Ledger & Journal if modified
                    if ($prevOpening !== $newOpening) {
                        CustomerController::syncOpeningBalance($customer, $newOpening);
                    }

                    $updated++;
                } else {
                    // Create new customer
                    $code = trim($row['customer_id'] ?? '');
                    if (empty($code) || Customer::where('customer_id', $code)->exists()) {
                        $code = $this->generateNextCustomerId();
                    }

                    $newOpening = (float)($row['opening_balance'] ?? 0);

                    $customer = Customer::create([
                        'customer_id'           => $code,
                        'customer_name'         => $row['customer_name'],
                        'customer_type'         => $row['customer_type'] ?: 'Main Customer',
                        'mobile'                => $row['mobile'] ?? null,
                        'zone'                  => $zoneId,
                        'address'               => $row['address'] ?? null,
                        'opening_balance'       => $newOpening,
                        'balance_range'         => (float)($row['balance_range'] ?? 0),
                        'reminder_day'          => $row['reminder_day'] ?? null,
                        'status'                => 'active',
                        'source'                => 'Manual',
                    ]);

                    if ($newOpening > 0) {
                        CustomerController::syncOpeningBalance($customer, $newOpening);
                    }

                    $created++;
                }
            }

            DB::commit();

            return [
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Auto-generate next customer_id like CUST-0003.
     */
    private function generateNextCustomerId(): string
    {
        $maxId = Customer::max('id') ?? 0;
        $candidateNum = $maxId + 1;

        do {
            $candidate = 'CUST-' . str_pad($candidateNum, 4, '0', STR_PAD_LEFT);
            if (!Customer::where('customer_id', $candidate)->exists()) {
                return $candidate;
            }
            $candidateNum++;
        } while (true);
    }

    /**
     * Auto-detect delimiter from first line of CSV.
     */
    private function detectDelimiter($handle): string
    {
        $pos = ftell($handle);
        $line = fgets($handle);
        fseek($handle, $pos);

        if (!$line) return ',';

        $commas = substr_count($line, ',');
        $semicolons = substr_count($line, ';');
        $tabs = substr_count($line, "\t");

        if ($semicolons > $commas && $semicolons > $tabs) return ';';
        if ($tabs > $commas && $tabs > $semicolons) return "\t";
        return ',';
    }

    /**
     * Map CSV header row to recognized field names with exact match first.
     */
    private function buildHeaderMap(array $headerRow): array
    {
        $map = [];
        $aliases = $this->headerAliases();

        // Pass 1: Exact matches
        foreach ($headerRow as $idx => $rawCol) {
            $colName = strtolower(trim($rawCol));
            $colName = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/u', '', $colName);
            $colName = trim($colName);

            foreach ($aliases as $fieldKey => $aliasList) {
                if (isset($map[$fieldKey])) continue;
                foreach ($aliasList as $alias) {
                    if ($colName === $alias) {
                        $map[$fieldKey] = $idx;
                        break;
                    }
                }
            }
        }

        // Pass 2: Prefix / fuzzy match for unmapped columns (e.g. "Full Name (*)")
        foreach ($headerRow as $idx => $rawCol) {
            if (in_array($idx, $map, true)) continue;

            $colName = strtolower(trim($rawCol));
            $colName = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/u', '', $colName);
            $colName = trim($colName);

            foreach ($aliases as $fieldKey => $aliasList) {
                if (isset($map[$fieldKey])) continue;
                foreach ($aliasList as $alias) {
                    if (str_starts_with($colName, $alias . ' ') || str_starts_with($colName, $alias . '(')) {
                        $map[$fieldKey] = $idx;
                        break;
                    }
                }
            }
        }

        return $map;
    }

    /**
     * Helper to safely extract value from CSV row.
     */
    private function getVal(array $row, string $fieldKey, array $headerMap, $default = '')
    {
        if (isset($headerMap[$fieldKey]) && isset($row[$headerMap[$fieldKey]])) {
            $val = trim($row[$headerMap[$fieldKey]]);
            return $val !== '' ? $val : $default;
        }
        return $default;
    }

    /**
     * Sanitize numeric inputs (e.g., "1,500.50" or PKR symbols -> 1500.50).
     */
    private function cleanNumber($val, $default = 0): float
    {
        if (is_numeric($val)) return (float)$val;
        if (empty($val) || strtolower(trim($val)) === 'unlimited') return (float)$default;

        $cleaned = preg_replace('/[^\d.-]/', '', (string)$val);
        return is_numeric($cleaned) ? (float)$cleaned : (float)$default;
    }
}

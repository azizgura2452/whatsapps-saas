<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\CustomerAttribute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class CustomerImportService
{
    public function importFromCsv(UploadedFile $file, ?int $broadcastGroupId = null): array
    {
        $imported = 0;
        $failed = 0;
        $errors = [];

        try {
            $handle = fopen($file->getRealPath(), 'r');
            
            // Read header row
            $headers = fgetcsv($handle);
            if (!$headers) {
                throw new \Exception('CSV file is empty or invalid');
            }

            // Normalize headers (trim and lowercase)
            $headers = array_map(fn($h) => trim(strtolower($h)), $headers);

            // Validate required columns - check for whatsapp_number or phone
            if (!in_array('whatsapp_number', $headers) && !in_array('phone', $headers) && !in_array('phone_number', $headers)) {
                throw new \Exception('CSV must contain a "whatsapp_number", "phone", or "phone_number" column');
            }

            // Determine which column contains the phone number
            $phoneIndex = false;
            if (($idx = array_search('whatsapp_number', $headers)) !== false) {
                $phoneIndex = $idx;
            } elseif (($idx = array_search('phone', $headers)) !== false) {
                $phoneIndex = $idx;
            } elseif (($idx = array_search('phone_number', $headers)) !== false) {
                $phoneIndex = $idx;
            }

            DB::beginTransaction();

            $rowNumber = 1; // Start from 1 (header is row 0)
            while (($row = fgetcsv($handle)) !== false) {
                $rowNumber++;
                
                try {
                    if (count($row) !== count($headers)) {
                        $failed++;
                        $errors[] = "Row {$rowNumber}: Column count mismatch";
                        continue;
                    }

                    $data = array_combine($headers, $row);
                    $phone = trim($data[$headers[$phoneIndex]]);

                    if (empty($phone)) {
                        $failed++;
                        $errors[] = "Row {$rowNumber}: Phone/WhatsApp number is empty";
                        continue;
                    }

                    // Prepare customer data
                    $customerData = [
                        'name' => $data['name'] ?? $data['customer_name'] ?? null,
                        'whatsapp_number' => $phone,
                    ];

                    // Add optional fields if they exist in CSV
                    if (isset($data['address'])) {
                        $customerData['address'] = trim($data['address']);
                    }
                    if (isset($data['birthday'])) {
                        $customerData['birthday'] = trim($data['birthday']);
                    }
                    if (isset($data['gender'])) {
                        $customerData['gender'] = trim($data['gender']);
                    }

                    // Create or update customer
                    $customer = Customer::updateOrCreate(
                        ['whatsapp_number' => $phone],
                        $customerData
                    );

                    // Import all other columns as attributes
                    foreach ($data as $key => $value) {
                        // Skip columns that are direct customer fields
                        if (in_array($key, [
                            'whatsapp_number', 
                            'phone', 
                            'phone_number', 
                            'name', 
                            'customer_name', 
                            'address', 
                            'birthday', 
                            'gender'
                        ])) {
                            continue;
                        }

                        if (!empty(trim($value))) {
                            CustomerAttribute::updateOrCreate(
                                [
                                    'customer_id' => $customer->id,
                                    'key' => $key,
                                ],
                                ['value' => trim($value)]
                            );
                        }
                    }

                    // Associate with broadcast group if provided
                    if ($broadcastGroupId) {
                        // Check if relationship already exists
                        $exists = DB::table('broadcast_group_customers')
                            ->where('broadcast_group_id', $broadcastGroupId)
                            ->where('customer_id', $customer->id)
                            ->exists();

                        if (!$exists) {
                            DB::table('broadcast_group_customers')->insert([
                                'broadcast_group_id' => $broadcastGroupId,
                                'customer_id' => $customer->id,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }

                    $imported++;
                } catch (\Exception $e) {
                    $failed++;
                    $errors[] = "Row {$rowNumber}: " . $e->getMessage();
                    Log::error('Customer import error', [
                        'row' => $rowNumber,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            fclose($handle);
            DB::commit();

            return [
                'success' => true,
                'imported' => $imported,
                'failed' => $failed,
                'errors' => $errors,
            ];
        } catch (\Exception $e) {
            if (isset($handle) && is_resource($handle)) {
                fclose($handle);
            }
            DB::rollBack();
            
            Log::error('CSV import failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'imported' => 0,
                'failed' => 0,
                'errors' => [$e->getMessage()],
            ];
        }
    }
}
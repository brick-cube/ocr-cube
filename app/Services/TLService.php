<?php

namespace App\Services;

use App\Models\Uploads;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TLService
{
    public static function test($path)
    {
        return "The file path given is: " . $path;
    }

    public static function uploadFile($filePath, $fileName)
    {
        $fileContent = Storage::get($filePath);

        $response = Http::withToken(config('oc.api_key.tensorlake'))
            ->timeout(30) // Matches CURLOPT_TIMEOUT
            ->attach(
                'file_bytes',   // The form field name
                $fileContent,     // The file content (raw string bytes)
                $fileName  // The filename
            )
            ->put('https://api.tensorlake.ai/documents/v2/files', [
                // Non-file form fields go here
                'labels' => json_encode([
                    'owner' => 'OCR cube',
                    'project' => "Jaison's Personal Project"
                ]),
            ]);

        if ($response->successful()) {
            Log::info($response->body());
        } else {
            Log::error($response->body());
        }

        return $response;
    }

    public static function createJob($fileId, $fileName, $schema)
    {
        //  TODO - Remove hardcoded values
        $fileId = 'file_nDPFMHrk6W8Wkr8rmDtLK';
        $targetFile = Uploads::where('tl_file_id', $fileId)->first();
        $fileName = '1.jpg';
        $schema = json_decode("{\"type\":\"object\",\"properties\":{\"total_cost\":{\"type\":\"number\"},\"receipt_number\":{\"type\":\"string\"},\"date\":{\"type\":\"string\"},\"items\":{\"type\":\"array\",\"items\":{\"type\":\"object\",\"properties\":{\"quantity\":{\"type\":\"number\"},\"price\":{\"type\":\"number\"},\"name\":{\"type\":\"string\"}}}},\"merchant_name\":{\"type\":\"string\"}},\"\$defs\":{},\"title\":\"receipt_details\"}");

        $response = Http::withToken(config('oc.api_key.tensorlake'))
            ->timeout(30)
            ->post('https://api.tensorlake.ai/documents/v2/extract', [
                'file_id' => $fileId,
                // 'page_range' => '<string>',
                'file_name' => $fileName,
                'mime_type' => 'image/jpeg',
                'structured_extraction_options' => [
                    [
                        'schema_name' => 'receipt_details',
                        // 'json_schema' => "{\"type\":\"object\",\"properties\":{\"total_cost\":{\"type\":\"number\"},\"receipt_number\":{\"type\":\"string\"},\"date\":{\"type\":\"string\"},\"items\":{\"type\":\"array\",\"items\":{\"type\":\"object\",\"properties\":{\"quantity\":{\"type\":\"number\"},\"price\":{\"type\":\"number\"},\"name\":{\"type\":\"string\"}}}},\"merchant_name\":{\"type\":\"string\"}},\"$defs\":{},\"title\":\"receipt_details\"}",
                        'json_schema' => $schema,
                        'skip_ocr' => true,
                        // 'prompt' => '<string>',
                        'model_provider' => 'tensorlake',
                        'partition_strategy' => 'none',
                        'page_classes' => null,
                        'provide_citations' => true
                    ]
                ],
                'labels' => [
                    'priority' => 'high',
                    'source' => 'email'
                ]
            ]);

        $targetFile->tl_job_id = $response->json()['parse_id'];
        $targetFile->update();

        if ($response->successful()) {
            Log::info($response->body());
        } else {
            Log::error('TL job failed. Reason: ' . $response->json()['error']);
            Log::error($response->body());
        }

        return $response;
    }

    public static function getResults($jobId)
    {
        //  TODO - Remove hardcoded values
        $jobId = 'parse_qHfwQTwMTjtRpMqkztpM6';

        $response = Http::withToken(config('oc.api_key.tensorlake'))
            ->timeout(30)
            ->get('https://api.tensorlake.ai/documents/v2/parse/' . $jobId);

        return $response;
    }
}

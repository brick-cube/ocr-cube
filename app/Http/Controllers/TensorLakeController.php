<?php

namespace App\Http\Controllers;

use App\Models\Uploads;
use App\Services\TLService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TensorLakeController extends Controller
{
    public function upload_page(Request $request)
    {
        return view('tensorlake/upload');
    }

    public function saveFile(Request $request)
    {
        $response = ['status' => 'success'];
        try {

            $path = $request->file('ocrFile')->store('uploads');
            $request->file('ocrFile')->getFilename();
            $ogName = $request->file('ocrFile')->getClientOriginalName();
            $fileName = $request->file('ocrFile')->getFilename();
            $f = Storage::path($path);
            $fileHash = sha1_file($f);

            $details = [$path, $ogName, $fileName, $fileHash];

            $uploadedFile = new Uploads;
            $uploadedFile->og_name = $ogName;
            $uploadedFile->name = $path;
            $uploadedFile->file_sha = $fileHash;
            $uploadedFile->status = 'pending';

            $tlResponse = TLService::uploadFile($path, $ogName);
            $uploadedFile->tl_file_id = $tlResponse->json()['file_id'];
            $uploadedFile->save();

            $details[] = $tlResponse;
        } catch (Exception $e) {
            Log::error($e);

            $response['status'] = 'error';
            return response()->json($response);
        }

        return response()->json($response);
    }

    public function processFile(Request $request)
    {
        $tlResponse = TLService::createJob('', '', '');
        return response($tlResponse);
    }

    public function getExtractedResults(Request $request)
    {
        $extractedResults = TLService::getResults('');

        return response()->json($extractedResults->json());
    }

    public function tlWebhook(Request $request)
    {
        Log::info('Request type"' . $request->method());
        Log::info($request->all());

        $targetFile = Uploads::where('tl_job_id', $request->job_id)->first();
        $targetFile->tl_status = $request->status;
        $targetFile->update();
    }
}

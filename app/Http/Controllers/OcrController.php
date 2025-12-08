<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\OcrFile;

class OcrController extends Controller
{
    public function index(Request $request)
    {
        $files = OcrFile::all();
        $selected = $request->selectedFile ?? 0;

        return view('ocr', [
            'files' => $files,
            'uploadedImage' => $files[$selected]->path ?? null,
            'fileName' => $files[$selected]->filename ?? null,
            'selected' => $selected,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'images.*' => 'required|file|mimes:jpg,jpeg,png,bmp,gif,tif,tiff,webp,pdf|max:4096'
        ]);

        $savedFiles = [];

        foreach ($request->file('images') as $file) {
            $path = $file->store('uploads', 'public');

            $saved = OcrFile::create([
                'path' => asset('storage/' . $path),
                'filename' => $file->getClientOriginalName(),
                'server_path' => storage_path('app/public/' . $path)
            ]);

            $savedFiles[] = $saved;
        }

        return redirect()->route('ocr.index');
    }

    public function select($index)
    {
        $files = OcrFile::all();
        if (!isset($files[$index])) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $file = $files[$index];

        return response()->json([
            'uploadedImage' => $file->path,
            'raw' => session('last_ocr_raw') ?? null,
            'selected' => $index
        ]);
    }


    public function extract(Request $request)
    {
        $files = OcrFile::all();
        $selected = $request->selectedFile;

        if (!isset($files[$selected])) {
            return redirect()->route('ocr.index');
        }

        $file = $files[$selected];

        $response = Http::withoutVerifying()
            ->timeout(90)
            ->asMultipart()
            ->post('https://api.ocr.space/parse/image', [
                ['name' => 'apikey', 'contents' => config('services.ocr.key')],
                ['name' => 'language', 'contents' => 'eng'],
                ['name' => 'isOverlayRequired', 'contents' => 'true'],
                ['name' => 'file', 'contents' => fopen($file->server_path, 'r'), 'filename' => $file->filename],
            ]);

        $result = $response->json();
        $parsed = $result['ParsedResults'][0] ?? null;

        if (!$parsed) {
            return back()->with('error', 'OCR failed.');
        }

        $text = $parsed['ParsedText'] ?? '';
        $lines = $parsed['TextOverlay']['Lines'] ?? [];

        $extractedFields = [
            'brand_name' => strtok($text, "\n"),
            'date' => $this->getMatch('/\d{4}[-\/.]\d{2}[-\/.]\d{2}/', $text),
            'phone' => $this->getMatch('/\+?[0-9\s]{8,15}/', $text),
            'email' => $this->getMatch('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $text),
            'total_amount' => $this->getMatch('/\b(\d+(\.\d{1,2})?)\s?(INR|USD|EUR)?/i', $text),
        ];

        $lineItems = [];
        foreach ($lines as $line) {
            $textLine = $line['LineText'] ?? '';
            if (preg_match('/^(.*?)(\d+(?:\.\d{1,2})?)$/', trim($textLine), $m)) {
                if (strlen(trim($m[1])) < 3) continue;

                $lineItems[] = [
                    'item' => trim($m[1]),
                    'qty' => 1,
                    'type' => 'Normal',
                    'amount' => trim($m[2]),
                ];
            }
        }

        return view('ocr', [
            'files' => $files,
            'uploadedImage' => $file->path,
            'fileName' => $file->filename,
            'text' => $text,
            'extractedFields' => $extractedFields,
            'lineItems' => $lineItems,
            'raw' => $result,
            'selected' => $selected,
        ]);
    }

    public function destroy($id)
    {
        $file = OcrFile::find($id);

        if (!$file) {
            return response()->json(['error' => 'File not found'], 404);
        }

        if (file_exists($file->server_path)) {
            unlink($file->server_path);
        }

        $file->delete();

        return response()->json(['success' => true]);
    }

    public function approve(Request $request)
    {
        $file = OcrFile::find($request->selectedFile);

        echo $file;

        if (!$file) return back()->with('error', 'File not found');

        // Save extracted fields
        $file->extracted_data = json_encode($request->except('_token', 'selectedFile'));
        $file->status = 'approved';
        $file->save();

        return redirect()->route('ocr.index')->with('success', 'File Approved & Data Saved');
    }



    private function getMatch($pattern, $text)
    {
        preg_match($pattern, $text, $matches);
        return $matches[0] ?? null;
    }
}

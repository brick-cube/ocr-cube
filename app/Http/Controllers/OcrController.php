<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OcrController extends Controller
{
    public function index(Request $request)
    {
        $files = session('ocr_files') ?? [];
        $selected = $request->selectedFile ?? session('selectedFile') ?? 0;

        session(['selectedFile' => $selected]);

        return view('ocr', [
            'uploadedImage' => $files[$selected]['path'] ?? null,
            'fileName' => $files[$selected]['filename'] ?? null,
            'text' => session('text'),
            'extractedFields' => session('extractedFields') ?? [],
            'lineItems' => session('lineItems') ?? [],
            'raw' => session('raw') ?? null,
            'selected' => $selected,
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'images.*' => 'required|file|mimes:jpg,jpeg,png,bmp,gif,tif,tiff,webp,pdf|max:4096'
        ]);

        $uploaded = [];

        foreach ($request->file('images') as $file) {
            $path = $file->store('uploads', 'public');

            $uploaded[] = [
                'path' => asset('storage/' . $path),
                'filename' => $file->getClientOriginalName(),
                'server_path' => storage_path('app/public/' . $path)
            ];
        }

        session(['ocr_files' => $uploaded, 'selectedFile' => 0]);
        return redirect()->route('ocr.index');
    }

    public function extract(Request $request)
    {
        $files = session('ocr_files');
        $selected = $request->selectedFile;

        if (!$files || !isset($files[$selected])) {
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
                ['name' => 'file', 'contents' => fopen($file['server_path'], 'r'), 'filename' => $file['filename']],
            ]);

        $result = $response->json();
        $parsed = $result['ParsedResults'][0] ?? null;

        if (!$parsed) {
            return back()->with('error', 'OCR failed.');
        }

        $text = $parsed['ParsedText'] ?? '';
        $lines = $parsed['TextOverlay']['Lines'] ?? [];

        // Extract fields smartly
        $extractedFields = [
            'merchant_name' => strtok($text, "\n"),
            'date' => $this->getMatch('/\d{4}[-\/.]\d{2}[-\/.]\d{2}/', $text),
            'phone' => $this->getMatch('/\+?[0-9\s]{8,15}/', $text),
            'email' => $this->getMatch('/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/i', $text),
            'total_amount' => $this->getMatch('/\b(\d+(\.\d{1,2})?)\s?(INR|USD|EUR)?/i', $text),
        ];

        // Line items
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

        // Save everything to session
        session([
            'text' => $text,
            'raw'  => $result,
            'extractedFields' => $extractedFields,
            'lineItems' => $lineItems,
            'selectedFile' => $selected,
        ]);

        return redirect()->route('ocr.index');
    }



    private function getMatch($pattern, $text)
    {
        preg_match($pattern, $text, $matches);
        return $matches[0] ?? null;
    }
}

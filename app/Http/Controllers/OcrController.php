<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OcrController extends Controller
{
    public function index()
    {
        return view('ocr', [
            'uploadedImage' => null,
            'fileName' => null,
            'text' => null,
            'extractedFields' => [],
            'lineItems' => [],
            'raw' => null,
        ]);
    }


    public function process(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,bmp,gif,tif,tiff,webp,pdf|max:4096',
        ]);

        // Store for preview
        $path = $request->file('image')->store('uploads', 'public');
        $uploadedImage = asset('storage/' . $path);
        $fileName = $request->file('image')->getClientOriginalName();

        $response = Http::timeout(90)
            ->asMultipart()
            ->withOptions(['verify' => false])
            ->post('https://api.ocr.space/parse/image', [
                ['name' => 'apikey', 'contents' => config('services.ocr.key')],
                ['name' => 'language', 'contents' => 'eng'],
                ['name' => 'isOverlayRequired', 'contents' => 'true'],
                ['name' => 'file', 'contents' => fopen(storage_path('app/public/' . $path), 'r'), 'filename' => $fileName],
            ]);

        $result = $response->json();
        $text = $result['ParsedResults'][0]['ParsedText'] ?? '';
        $lines = $result['ParsedResults'][0]['TextOverlay']['Lines'] ?? [];

        // Extract fields automatically
        $extractedFields = [
            'merchant_name'   => strtok($text, "\n"), // first line
            'merchant_address' => $this->getMatch('/[a-zA-Z0-9 _.,-]+ u, [0-9.]+/i', $text),
            'date'            => $this->getMatch('/\d{4}[.\-\/]\d{2}[.\-\/]\d{2}/', $text),
            'total_amount'    => $this->getMatch('/\b(\d{3,6})\b(?=\s*Ft)/', $text),
            'currency'        => $this->getMatch('/\b(?:Ft|EUR|USD|INR|GBP)\b/', $text),
        ];

        // Extract line items (anything with multiple words and alphabetic)
        // Extract line items with item name + amount
        $lineItems = [];

        foreach ($lines as $line) {
            $textLine = $line['LineText'] ?? ''; // actual text string

            if (preg_match('/^(.*?)(\d+(?:\.\d{1,2})?)$/', trim($textLine), $m)) {
                $itemName = trim($m[1]);
                $amount = trim($m[2]);

                if (strlen($itemName) < 3) continue;

                $lineItems[] = [
                    'item'   => $itemName,
                    'qty'    => 1,
                    'type'   => 'Normal',
                    'amount' => $amount,
                ];
            }
        }



        return view('ocr', [
            'text' => $text,
            'raw' => $result,
            'uploadedImage' => $uploadedImage,
            'fileName' => $fileName,
            'extractedFields' => $extractedFields,
            'lineItems' => $lineItems,
        ]);
    }

    private function getMatch($pattern, $text)
    {
        preg_match($pattern, $text, $matches);
        return $matches[0] ?? null;
    }
}

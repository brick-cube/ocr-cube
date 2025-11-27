{{-- components/ocr/fields.blade.php --}}

<article class="w-full border-y border-[#f1f1f1] mt-3 py-3">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-[16px] text-[#575656] flex items-center gap-3">
            EXTRACTED FIELDS
            <button
                class="px-2 py-[6px] rounded-[5px] bg-[#f8f7f7] text-[13px] text-[#575656] font-semibold">
                <i class="fa-solid fa-pen text-xs mr-1"></i>
                Edit
            </button>
        </h3>
        <span class="text-[#575656] text-sm">&#9660;</span>
    </div>

    @php
        $fields = [
            'Merchant Name' => $extractedFields['merchant_name'] ?? null,
            'Phone' => $extractedFields['phone'] ?? null,
            'Email' => $extractedFields['email'] ?? null,
            'Website' => $extractedFields['website'] ?? null,
            'Address' => $extractedFields['merchant_address'] ?? null,
            'Date' => $extractedFields['date'] ?? null,
            'Total Amount' => $extractedFields['total_amount'] ?? null,
        ];
    @endphp

    @foreach ($fields as $label => $value)
        @continue(!$value)
        <div class="grid grid-cols-[120px_minmax(0,1fr)] items-center gap-[6px] mb-3">
            <p class="text-[13px] font-medium text-[#929292]">{{ $label }}</p>
            <input type="text" value="{{ $value }}"
                   class="w-full bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold text-[#535353]">
        </div>
    @endforeach

    @if(empty(array_filter($fields)))
        <p class="text-xs text-gray-400">No structured fields detected yet.</p>
    @endif
</article>

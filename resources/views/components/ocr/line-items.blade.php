<article class="w-full border-b border-[#f1f1f1] py-3">
    <div class="flex items-center justify-between mb-3">
        <h3 class="text-[16px] text-[#575656] flex items-center gap-3">
            LINE ITEMS
            <button class="px-2 py-[6px] rounded-[5px] bg-[#f8f7f7] text-[13px] font-semibold flex items-center gap-1">
                <i class="fa-solid fa-pen text-xs"></i> Edit
            </button>
        </h3>
        <span class="text-[#575656] text-sm">&#9660;</span>
    </div>

    {{-- SAFE DEFAULT --}}
    @php $lineItems = $lineItems ?? []; @endphp

    @if(count($lineItems) === 0)
        <p class="text-xs text-gray-400">No line items detected</p>
    @endif

    @if(count($lineItems) > 0)
        {{-- Table Header --}}
        <div class="grid grid-cols-4 text-[12px] font-semibold text-[#929292] mb-2">
            <p>Item</p>
            <p>Qty</p>
            <p>Type</p>
            <p>Amount</p>
        </div>

        {{-- Rows --}}
        @foreach ($lineItems as $row)
            <div class="grid grid-cols-4 gap-2 mb-2">
                <input
                    type="text"
                    value="{{ $row['item'] ?? '' }}"
                    class="bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold"
                >

                <input
                    type="number"
                    value="{{ $row['qty'] ?? 1 }}"
                    class="bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold"
                >

                <select
                    class="bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold"
                >
                    <option {{ ($row['type'] ?? '') == 'Normal' ? 'selected' : '' }}>Normal</option>
                    <option {{ ($row['type'] ?? '') == 'Takeaway' ? 'selected' : '' }}>Takeaway</option>
                    <option {{ ($row['type'] ?? '') == 'Delivery' ? 'selected' : '' }}>Delivery</option>
                </select>

                <input
                    type="text"
                    value="{{ $row['amount'] ?? '' }}"
                    class="bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold"
                >
            </div>
        @endforeach
    @endif
</article>

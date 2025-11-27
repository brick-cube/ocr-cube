<section class="w-64 border-r border-[#f1f1f1]">
    <div class="px-4 py-4 flex items-center gap-2 text-[#c5c4c4] font-semibold text-sm">
        <i class="fa-solid fa-magnifying-glass text-xs"></i>
        <input type="search" placeholder="Search"
            class="w-full border-none outline-none text-sm font-semibold placeholder-[#e6e5e5] text-[#575656]">
    </div>

    <div class="flex items-center justify-between px-3 py-3 border-y border-[#f1f1f1] gap-6">
        <button class="px-3 py-[6px] border border-[#f1f1f1] bg-[#fcfcfc] text-[13px] font-semibold rounded-[2px]">
            All (1) ▼
        </button>
        <div class="flex">
            <button class="px-3 py-[6px] border border-[#f1f1f1]">▲</button>
            <button class="px-3 py-[6px] border-y border-[#f1f1f1]">1/1</button>
            <button class="px-3 py-[6px] border border-[#f1f1f1]">▼</button>
        </div>
    </div>

    <div class="px-2 py-3 space-y-3">
        <div class="relative border border-[#f1f1f1] rounded-[6px] px-2 py-2 flex items-center justify-center">
            <span class="absolute top-2 left-3 bg-[rgb(195,255,195)] text-[10px] font-semibold text-green-600 px-2 py-[2px] rounded-full">
                {{ isset($text) ? 'Approved' : 'Pending' }}
            </span>

            @isset($uploadedImage)
            <img src="{{ $uploadedImage }}" class="h-40 object-contain rounded" />
            @else
            <img src="{{ asset('receipt.png') }}" class="h-40 object-contain opacity-60" />
            @endisset
        </div>
    </div>
</section>

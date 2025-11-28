<section class="w-64 border-r border-[#f1f1f1]">
    @php
        $files = session('ocr_files') ?? [];
        $selected = session('selectedFile') ?? 0;
        $total = count($files);
    @endphp

    {{-- SEARCH --}}
    <div class="px-4 py-4 flex items-center gap-2 text-[#c5c4c4] font-semibold text-sm">
        <i class="fa-solid fa-magnifying-glass text-xs"></i>
        <input
            type="search"
            id="fileSearch"
            placeholder="Search file..."
            onkeyup="filterFiles()"
            class="w-full border-none outline-none text-sm font-semibold placeholder-[#e6e5e5] text-[#575656]"
        >
    </div>

    {{-- FILTER HEADER + ARROWS --}}
    <div class="flex items-center justify-between px-3 py-3 border-y border-[#f1f1f1] gap-6">
        <button class="px-3 py-[6px] border border-[#f1f1f1] bg-[#fcfcfc] text-[13px] font-semibold rounded-[2px]">
            All ({{ $total }})
        </button>

        <div class="flex">
            <button
                onclick="navigateFile('prev')"
                class="px-2 py-[4px] border border-[#f1f1f1] {{ $selected === 0 ? 'opacity-40 cursor-not-allowed' : '' }}">
                ▲
            </button>

            <button class="px-2 py-[4px] border-y border-[#f1f1f1] text-[13px]">
                {{ $selected + 1 }}/{{ $total }}
            </button>

            <button
                onclick="navigateFile('next')"
                class="px-2 py-[4px] border border-[#f1f1f1] {{ $selected === $total-1 ? 'opacity-40 cursor-not-allowed' : '' }}">
                ▼
            </button>
        </div>
    </div>

    {{-- FILE LIST --}}
    <div id="fileList" class="px-2 py-3 space-y-3">
        @foreach ($files as $index => $f)
            <div
                class="relative rounded-[6px] px-2 py-2 flex items-center justify-center cursor-pointer transition-all duration-150
                {{ $index == $selected ? 'border-[2px] border-[#3b2df5] shadow-md' : 'border border-[#f1f1f1]' }}"
                onclick="window.location='{{ route('ocr.index') }}?selectedFile={{ $index }}'"
                data-name="{{ strtolower($f['filename']) }}"
            >
                <span
                    class="absolute top-2 left-3 text-[10px] font-semibold px-2 py-[2px] rounded-full
                    {{ $index == $selected ? 'bg-[rgb(195,255,195)] text-green-600' : 'bg-[rgb(255,234,167)] text-orange-500' }}">
                    {{ $index == $selected ? 'Selected' : 'Pending' }}
                </span>

                <img src="{{ $f['path'] }}" class="h-40 object-contain rounded">
            </div>
        @endforeach
    </div>
</section>

<script>
    // SEARCH FILTER
    function filterFiles() {
        const term = document.getElementById("fileSearch").value.toLowerCase();
        const items = document.querySelectorAll('#fileList > div');

        items.forEach(item => {
            item.style.display = item.dataset.name.includes(term) ? '' : 'none';
        });
    }

    // ARROWS NEXT / PREV
    function navigateFile(direction) {
        const selected =  $selected ;
        const total =  $total ;

        let newIndex = selected;
        if (direction === "prev" && selected > 0) newIndex--;
        if (direction === "next" && selected < total - 1) newIndex++;

        window.location = "{{ route('ocr.index') }}?selectedFile=" + newIndex;
    }
</script>

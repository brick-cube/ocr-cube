<section class="flex flex-col items-center justify-start bg-[#f3f3f3] w-[52%] py-9">
    <div id="imageContainer"
        class="relative flex items-center justify-center bg-white/60 rounded-md overflow-hidden"
        style="cursor: grab;">

        @isset($uploadedImage)
            <img src="{{ $uploadedImage }}" id="preview"
                class="max-h-[70vh] w-auto object-contain rounded shadow transition-transform duration-200"
                style="transform: scale(1);">
        @else
            <p class="px-8 py-6 text-sm text-gray-400">Upload a receipt to preview & extract text</p>
        @endisset
    </div>

    @isset($uploadedImage)
        {{-- ZOOM CONTROLS --}}
        <div class="flex items-center gap-3 mt-6 bg-black text-[#bebebe] px-4 py-[6px] rounded-[6px] border border-[#ccc]">
            <button id="zoomOutBtn" class="text-lg font-bold select-none">−</button>

            <div class="zoom-bar flex-1 h-[6px] bg-[#707070] rounded-[4px] relative overflow-hidden mx-2">
                <div id="zoomFill"
                    class="absolute left-0 top-0 h-full bg-white rounded-[4px] w-[25%] transition-[width] duration-200"></div>
            </div>

            <button id="zoomInBtn" class="text-lg font-bold select-none">+</button>
            <i id="resetZoomBtn" class="fa-solid fa-rotate-right text-sm cursor-pointer ml-2"></i>
        </div>
    @endisset
</section>

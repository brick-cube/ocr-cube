@if(isset($raw['ParsedResults'][0]['TextOverlay']['Lines']))
<div id="ocrData" data-lines='@json($raw["ParsedResults"][0]["TextOverlay"]["Lines"])'></div>
@endif
<section class="flex flex-col items-center justify-start bg-[#f3f3f3] w-[52%] py-9">

    {{-- Hidden OCR JSON container --}}
    @if(isset($raw['ParsedResults'][0]['TextOverlay']['Lines']))
    <div id="ocrData" data-lines='@json($raw["ParsedResults"][0]["TextOverlay"]["Lines"])'></div>
    @endif

    {{-- IMAGE + OVERLAY WRAPPER --}}
    <div id="imageContainer"
        class="relative flex items-center justify-center bg-white/60 rounded-md overflow-hidden"
        style="cursor: grab;">

        @isset($uploadedImage)
        <img src="{{ $uploadedImage }}" id="preview"
            class="max-h-[70vh] w-auto object-contain rounded shadow transition-transform duration-200"
            style="transform: scale(1);">

        <div id="ocrOverlay" class="absolute inset-0 pointer-events-none"></div>
        @else
        <p class="px-8 py-6 text-sm text-gray-400">Upload a receipt to preview & extract text</p>
        @endisset
    </div>

    {{-- ZOOM CONTROLS --}}
    @isset($uploadedImage)
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

    {{-- OCR OVERLAY SCRIPT --}}
    @if(isset($raw['ParsedResults'][0]['TextOverlay']['Lines']))
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const overlay = document.getElementById("ocrOverlay");
            const previewImg = document.getElementById("preview");
            const dataBox = document.getElementById("ocrData");

            if (!overlay || !previewImg || !dataBox) return;

            const ocrLines = JSON.parse(dataBox.dataset.lines);

            function drawOverlay() {
                const containerRect = previewImg.parentElement.getBoundingClientRect();
                const imgRect = previewImg.getBoundingClientRect();

                const naturalWidth = previewImg.naturalWidth;
                const naturalHeight = previewImg.naturalHeight;

                const displayWidth = imgRect.width;
                const displayHeight = imgRect.height;

                if (displayWidth === 0 || displayHeight === 0) {
                    requestAnimationFrame(drawOverlay);
                    return;
                }

                const xScale = displayWidth / naturalWidth;
                const yScale = displayHeight / naturalHeight;

                const leftOffset = imgRect.left - containerRect.left;
                const topOffset = imgRect.top - containerRect.top;

                overlay.innerHTML = ""; 

                ocrLines.forEach(line => {
                    line.Words.forEach(word => {
                        const box = document.createElement("div");
                        box.className = "absolute border border-orange-300 bg-orange-500/20";
                        box.style.left = (word.Left * xScale + leftOffset) + "px";
                        box.style.top = (word.Top * yScale + topOffset) + "px";
                        box.style.width = (word.Width * xScale) + "px";
                        box.style.height = (word.Height * yScale) + "px";
                        overlay.appendChild(box);
                    });
                });
            }

            requestAnimationFrame(drawOverlay);
        });
    </script>


    @endif
</section>
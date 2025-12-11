{{-- OCR DATA CONTAINER --}}
@if(isset($raw['ParsedResults'][0]['TextOverlay']['Lines']))
    <div id="ocrData" data-lines='@json($raw["ParsedResults"][0]["TextOverlay"]["Lines"])'></div>
    <div id="ocrOrientation" data-orientation="{{ $raw['ParsedResults'][0]['TextOrientation'] ?? 0 }}"></div>
@endif

<section class="flex flex-col items-center justify-start bg-[#f3f3f3] w-[52%] py-9 px-4">

    {{-- IMAGE + OVERLAY WRAPPER --}}
    <div id="imageContainer"
        class="relative flex items-center justify-center bg-white/60 rounded-md overflow-hidden group w-full"
        style="cursor: pointer;">

        {{-- Upload Form --}}
        <form id="uploadForm" action="{{ route('ocr.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="file" id="uploadInput" name="images[]" class="hidden" multiple>
        </form>

        @isset($uploadedImage)
            <img src="{{ $uploadedImage }}" id="preview"
                 class="max-h-[70vh] w-auto object-contain rounded shadow transition-transform duration-200"
                 style="transform: scale(1);">

            <div id="ocrOverlay" class="absolute inset-0 pointer-events-none"></div>

            {{-- Floating hint --}}
            <div class="absolute top-2 right-2 bg-black/60 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition">
                Click to add more
            </div>
        @else
            {{-- DROP ZONE --}}
            <div id="dropZone"
                class="flex flex-col items-center justify-center w-full h-[60vh] border-2 border-dashed border-[#c8c8c8] text-center
                transition-all duration-200 hover:border-[#3b2df5] cursor-pointer select-none">

                <p class="text-sm text-gray-400">Drop receipt here or click to upload</p>
            </div>
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
</section>

{{-- IMAGE UPLOAD + DRAG & DROP, --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const container = document.getElementById("imageContainer");
    const dropZone = document.getElementById("dropZone");
    const uploadInput = document.getElementById("uploadInput");
    const uploadForm = document.getElementById("uploadForm");

    // CLICK to upload
    container.addEventListener("click", (e) => {
        if (e.target.id !== "preview") uploadInput.click();
    });

    uploadInput.addEventListener("change", () => uploadForm.submit());

    // DRAG & DROP
    if (dropZone) {
        dropZone.addEventListener("dragover", (e) => {
            e.preventDefault();
            dropZone.classList.add("border-[#3b2df5]", "bg-[#eef1ff]");
        });

        dropZone.addEventListener("dragleave", () => {
            dropZone.classList.remove("border-[#3b2df5]", "bg-[#eef1ff]");
        });

        dropZone.addEventListener("drop", (e) => {
            e.preventDefault();
            dropZone.classList.remove("border-[#3b2df5]", "bg-[#eef1ff]");
            uploadInput.files = e.dataTransfer.files;
            uploadForm.submit();
        });
    }
});
</script>

{{-- FIX OCR BOXES FOR HORIZONTAL IMAGES --}}
@if(isset($raw['ParsedResults'][0]['TextOverlay']['Lines']))
<script>
document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.getElementById("ocrOverlay");
    const previewImg = document.getElementById("preview");
    const dataBox = document.getElementById("ocrData");
    const orientationBox = document.getElementById("ocrOrientation");

    if (!overlay || !previewImg || !dataBox) return;

    const ocrLines = JSON.parse(dataBox.dataset.lines);
    const orientation = parseInt(orientationBox.dataset.orientation || 0);

    function drawOverlay() {
        const container = previewImg.parentElement;
        const containerRect = container.getBoundingClientRect();
        const imgRect = previewImg.getBoundingClientRect();

        const naturalWidth = previewImg.naturalWidth;
        const naturalHeight = previewImg.naturalHeight;

        const displayWidth = imgRect.width;
        const displayHeight = imgRect.height;

        const xScale = displayWidth / naturalWidth;
        const yScale = displayHeight / naturalHeight;

        const leftOffset = (containerRect.width - displayWidth) / 2;
        const topOffset = (containerRect.height - displayHeight) / 2;

        overlay.innerHTML = "";

        ocrLines.forEach(line => {
            line.Words.forEach(word => {
                let left = word.Left;
                let top = word.Top;
                let width = word.Width;
                let height = word.Height;

                if (orientation === 90) {
                    left = naturalHeight - (word.Top + word.Height);
                    top = word.Left;
                    width = word.Height;
                    height = word.Width;
                } else if (orientation === 180) {
                    left = naturalWidth - (word.Left + word.Width);
                    top = naturalHeight - (word.Top + word.Height);
                } else if (orientation === 270) {
                    left = word.Top;
                    top = naturalWidth - (word.Left + word.Width);
                    width = word.Height;
                    height = word.Width;
                }

                const box = document.createElement("div");
                box.className = "absolute border border-orange-400 bg-orange-500/20";

                box.style.left = (left * xScale + leftOffset) + "px";
                box.style.top = (top * yScale + topOffset) + "px";
                box.style.width = (width * xScale) + "px";
                box.style.height = (height * yScale) + "px";

                overlay.appendChild(box);
            });
        });
    }

    requestAnimationFrame(drawOverlay);
});
</script>
@endif

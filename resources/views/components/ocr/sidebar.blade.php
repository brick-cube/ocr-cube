<section class="w-64 border-r border-[#f1f1f1] flex flex-col">

    @php $total = count($files); @endphp

    {{-- SEARCH + SORT --}}
    <div class="px-4 py-4 space-y-3">

        {{-- SEARCH BAR --}}
        <div class="flex items-center gap-2 text-[#c5c4c4] font-semibold text-sm">
            <i class="fa-solid fa-magnifying-glass text-xs"></i>
            <input type="search" id="fileSearch" placeholder="Search file..."
                onkeyup="filterFiles()"
                class="w-full border-none outline-none text-sm font-semibold placeholder-[#e6e5e5] text-[#575656]">
        </div>

    </div>

    {{-- HEADER --}}
    <div class="flex items-center justify-between px-3 py-3 border-y border-[#f1f1f1] gap-6">
        <button class="px-3 py-[6px] border border-[#f1f1f1] bg-[#fcfcfc] text-[13px] font-semibold rounded-[2px]">
            All ({{ $total }})
        </button>

        <div class="flex">
            <button onclick="navigateFile('prev')" class="px-2 py-[4px] border border-[#f1f1f1]">▲</button>
            <button id="positionText" class="px-2 py-[4px] border-y border-[#f1f1f1] text-[13px]">
                {{ $total > 0 ? $selected + 1 : 0 }}/{{ $total }}
            </button>
            <button onclick="navigateFile('next')" class="px-2 py-[4px] border border-[#f1f1f1]">▼</button>
        </div>
    </div>

    {{-- FILE LIST --}}
    <div id="fileList" class="px-2 py-3 space-y-3 overflow-y-auto">
        @foreach ($files as $index => $f)
        <div id="file-{{ $f->id }}"
            class="file-item {{ $index == $selected ? 'selected-item' : '' }}
            relative rounded-[6px] p-2 flex flex-col items-center cursor-pointer transition-all duration-150 border border-[#f1f1f1]"
            onclick="selectFile({{ $index }})"
            data-name="{{ strtolower($f->filename) }}"
            data-date="{{ $f->created_at }}">

            <span class="status-badge absolute top-2 left-2 text-[10px] font-semibold px-2 py-[2px] rounded-full
    {{ $index == $selected ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }}">
                {{ $index == $selected ? 'Selected' : 'Pending' }}
            </span>


            {{-- DELETE BUTTON --}}
            <button type="button"
                class="delete-btn absolute top-2 right-2 z-20 border border-red-300 bg-red-50 text-red-600 text-xs px-2 py-[2px] rounded hover:bg-red-100"
                onclick="event.stopPropagation(); openDeleteModal({{ $f->id }})">✖</button>

            <img src="{{ $f->path }}" class="h-32 object-contain rounded mb-2">

            <p class="text-[11px] font-medium text-center truncate w-full">
                {{ $f->filename }}
            </p>

        </div>
        @endforeach
    </div>
</section>

{{-- 🔥 DELETE MODAL (moved outside!) --}}
<div id="deleteModal"
    class="fixed inset-0 bg-black/40 flex items-center justify-center z-[999] hidden">
    <div class="bg-white p-6 rounded-lg shadow-xl w-72">
        <h2 class="text-lg font-semibold text-gray-800 mb-3">Delete File?</h2>
        <p class="text-sm text-gray-500 mb-4">Are you sure you want to remove this receipt?</p>

        <div class="flex justify-end gap-2">
            <button onclick="closeDeleteModal()" class="px-4 py-1.5 text-sm border rounded">Cancel</button>
            <button onclick="confirmDelete()" class="px-4 py-1.5 text-sm bg-red-600 text-white rounded">Delete</button>
        </div>
    </div>
</div>

<style>
    .selected-item {
        border: 2px solid #3b2df5 !important;
        box-shadow: 0 0 6px rgba(59, 45, 245, 0.5);
    }
</style>


<script>
    let deleteId = null;

    function openDeleteModal(id) {
        deleteId = id;
        document.getElementById("deleteModal").classList.remove("hidden");
    }

    function closeDeleteModal() {
        deleteId = null;
        document.getElementById("deleteModal").classList.add("hidden");
    }

    function confirmDelete() {
        fetch(`/ocr/${deleteId}`, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(res => res.json())
            .then(() => {
                removeFileFromUI(deleteId);
                closeDeleteModal();
            });
    }

    function removeFileFromUI(id) {
        const item = document.getElementById(`file-${id}`);
        const items = [...document.querySelectorAll("#fileList .file-item")];
        const deletedIndex = items.indexOf(item);

        item.remove();

        const remaining = [...document.querySelectorAll("#fileList .file-item")];
        if (remaining.length > 0) {
            selectFile(Math.max(0, deletedIndex - 1));
            document.getElementById("positionText").innerText =
                `${Math.max(0, deletedIndex)}/${remaining.length}`;
        } else {
            location.reload();
        }
    }

    async function selectFile(index) {
        document.getElementById("selectedFileInput").value = index;

        const response = await fetch(`/ocr/select/${index}`);
        const data = await response.json();

        const container = document.getElementById("imageContainer");
        container.innerHTML = `
        <img src="${data.uploadedImage}" id="preview"
            class="max-h-[70vh] w-auto object-contain rounded shadow transition-transform duration-200">
        <div id="ocrOverlay" class="absolute inset-0 pointer-events-none"></div>
    `;

        document.querySelectorAll("#fileList .file-item").forEach((el, i) => {
            el.classList.toggle("selected-item", i === index);
        });

        document.querySelectorAll("#fileList .file-item").forEach((el, i) => {
            const badge = el.querySelector(".status-badge");

            if (i === index) {
                el.classList.add("selected-item");
                badge.innerText = "Selected";
                badge.classList.remove("bg-yellow-100", "text-yellow-600");
                badge.classList.add("bg-green-100", "text-green-600");
            } else {
                el.classList.remove("selected-item");
                badge.innerText = "Pending";
                badge.classList.remove("bg-green-100", "text-green-600");
                badge.classList.add("bg-yellow-100", "text-yellow-600");
            }
        });

        document.getElementById("positionText").innerText = `${index+1}/${remainingFilesCount()}`;

        if (data.raw) {
            drawOverlayFromRaw(data.raw);
        }
    }


    function navigateFile(direction) {
        const items = [...document.querySelectorAll("#fileList .file-item")];
        let current = items.findIndex(el => el.classList.contains("selected-item"));

        let newIndex = direction === "prev" ? current - 1 : current + 1;
        if (newIndex >= 0 && newIndex < items.length) {
            selectFile(newIndex);
        }
    }

    function remainingFilesCount() {
        return document.querySelectorAll("#fileList .file-item").length;
    }

    function filterFiles() {
        const term = document.getElementById("fileSearch").value.toLowerCase();
        document.querySelectorAll('.file-item').forEach(item => {
            item.style.display = item.dataset.name.includes(term) ? '' : 'none';
        });
    }
</script>
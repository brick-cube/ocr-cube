<article class="w-full border-y border-[#f1f1f1] mt-3 py-3">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-3 cursor-pointer"
        onclick="toggleFields()">
        <div class="flex items-center gap-2">
            <h3 class="text-[16px] text-[#575656] flex items-center gap-3">
                EXTRACTED FIELDS
            </h3>
            <button id="editBtn"
                onclick="toggleEditMode(event)"
                class="px-2 py-[6px] rounded-[5px] bg-[#f8f7f7] text-[13px] text-[#575656] font-semibold">
                <i class="fa-solid fa-pen text-xs mr-1"></i> Edit
            </button>
        </div>

        <span id="fieldArrow"
            class="text-[#575656] text-sm transition-transform">&#9660;</span>
    </div>

    {{-- FIELD LIST --}}
    <div id="fieldsContainer">

        @php
        $fields = [
        'Brand Name' => $extractedFields['brand_name'] ?? null,
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
        <div class="grid grid-cols-[120px_minmax(0,1fr)_30px] items-center gap-[6px] mb-3 field-row">
            <p class="text-[13px] font-medium text-[#929292]">{{ $label }}</p>
            <input type="text" value="{{ $value }}"
                class="field-input w-full bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold text-[#535353]"
                disabled>

            {{-- DELETE BUTTON (hidden until edit mode) --}}
            <button class="delete-btn hidden text-red-500 text-xs font-bold">✕</button>
        </div>
        @endforeach

        @if(empty(array_filter($fields)))
        <p class="text-xs text-gray-400">No structured fields detected yet.</p>
        @endif

        {{-- ADD FIELD BUTTON --}}
        <button id="addFieldBtn"
            class="hidden mt-2 text-[13px] font-semibold text-[#3b2df5]">
            + Add Field
        </button>
    </div>
</article>

<script>
    let isCollapsed = false;
    let editMode = false;

    function toggleFields() {
        const container = document.getElementById("fieldsContainer");
        const arrow = document.getElementById("fieldArrow");

        isCollapsed = !isCollapsed;
        container.style.display = isCollapsed ? "none" : "block";
        arrow.style.transform = isCollapsed ? "rotate(-90deg)" : "rotate(0deg)";
    }

    function toggleEditMode(event) {
        event.stopPropagation(); // don't collapse panel

        editMode = !editMode;

        const inputFields = document.querySelectorAll(".field-input");
        const deleteBtns = document.querySelectorAll(".delete-btn");
        const addBtn = document.getElementById("addFieldBtn");
        const editBtn = document.getElementById("editBtn");

        inputFields.forEach(input => input.disabled = !editMode);
        deleteBtns.forEach(btn => btn.classList.toggle("hidden", !editMode));
        addBtn.classList.toggle("hidden", !editMode);

        editBtn.innerHTML = editMode ?
            `<i class='fa-solid fa-check text-xs mr-1'></i> Save` :
            `<i class='fa-solid fa-pen text-xs mr-1'></i> Edit`;
    }
</script>
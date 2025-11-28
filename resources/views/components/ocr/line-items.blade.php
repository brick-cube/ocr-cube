<article class="w-full border-b border-[#f1f1f1] py-3">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-3 cursor-pointer"
        onclick="toggleLineItems()">
        <div class="flex items-center gap-2">
        <h3 class="text-[16px] text-[#575656] flex items-center gap-3">
            LINE ITEMS
        </h3>

            <button id="editLineBtn"
                onclick="toggleLineEdit(event)"
                class="px-2 py-[6px] rounded-[5px] bg-[#f8f7f7] text-[13px] font-semibold flex items-center gap-1 text-[#575656]">
                <i class="fa-solid fa-pen text-xs"></i>
                Edit
            </button>
        </div>

            <span id="lineArrow"
                class="text-[#575656] text-sm transition-transform">&#9660;</span>
    </div>

    {{-- CONTAINER --}}
    <div id="lineContainer">

        {{-- SAFE DEFAULT --}}
        @php $lineItems = $lineItems ?? []; @endphp

        @if(count($lineItems) === 0)
            <p class="text-xs text-gray-400">No line items detected</p>
        @endif

        @if(count($lineItems) > 0)

            {{-- TABLE HEADER --}}
            <div class="grid grid-cols-4 text-[12px] font-semibold text-[#929292] mb-2">
                <p>Item</p>
                <p>Qty</p>
                <p>Type</p>
                <p class="flex justify-between items-center">Amount</p>
            </div>

            {{-- ROWS --}}
            @foreach ($lineItems as $row)
                <div class="grid grid-cols-4 gap-2 mb-2 line-row">

                    <input type="text" value="{{ $row['item'] ?? '' }}"
                        class="line-input bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold"
                        disabled>

                    <input type="number" value="{{ $row['qty'] ?? 1 }}"
                        class="line-input bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold"
                        disabled>

                    <select class="line-input bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold" disabled>
                        <option {{ ($row['type'] ?? '') == 'Normal' ? 'selected' : '' }}>Normal</option>
                        <option {{ ($row['type'] ?? '') == 'Takeaway' ? 'selected' : '' }}>Takeaway</option>
                        <option {{ ($row['type'] ?? '') == 'Delivery' ? 'selected' : '' }}>Delivery</option>
                    </select>

                    <div class="flex items-center justify-between gap-2">
                        <input type="text" value="{{ $row['amount'] ?? '' }}"
                            class="line-input bg-[#f8f8f8] px-2 py-1 rounded text-[12px] font-semibold"
                            disabled>

                        {{-- DELETE BUTTON --}}
                        <button class="delete-line-btn hidden text-red-500 text-xs font-bold">✕</button>
                    </div>
                </div>
            @endforeach

        @endif

        {{-- ADD ROW BUTTON --}}
        <button id="addLineBtn"
            class="hidden mt-2 text-[13px] font-semibold text-[#3b2df5]">
            + Add Line Item
        </button>
    </div>
</article>

<script>
    let lineCollapsed = false;
    let lineEditMode = false;

    function toggleLineItems() {
        const box = document.getElementById("lineContainer");
        const arrow = document.getElementById("lineArrow");

        lineCollapsed = !lineCollapsed;
        box.style.display = lineCollapsed ? "none" : "block";
        arrow.style.transform = lineCollapsed ? "rotate(-90deg)" : "rotate(0deg)";
    }

    function toggleLineEdit(event) {
        event.stopPropagation();

        lineEditMode = !lineEditMode;

        const edits = document.querySelectorAll(".line-input");
        const deleteBtns = document.querySelectorAll(".delete-line-btn");
        const addBtn = document.getElementById("addLineBtn");
        const editBtn = document.getElementById("editLineBtn");

        edits.forEach(e => e.disabled = !lineEditMode);
        deleteBtns.forEach(btn => btn.classList.toggle("hidden", !lineEditMode));
        addBtn.classList.toggle("hidden", !lineEditMode);

        editBtn.innerHTML = lineEditMode
            ? "<i class='fa-solid fa-check text-xs mr-1'></i>Save"
            : "<i class='fa-solid fa-pen text-xs mr-1'></i>Edit";
    }
</script>

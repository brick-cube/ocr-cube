<header class="flex items-center justify-between px-5 py-1 border-b border-[#f1f1f1]">
    <nav>
        <p class="m-0 text-[15px] text-gray-500">
            Brick-cube >
            <span class="text-black font-medium">Multi Receipts OCR Parsing</span>
        </p>
    </nav>

    <div class="flex items-center gap-1 text-[#6d6c6c]">

        <label class="px-3 py-[6px] rounded-[5px] bg-[#3b2df5] text-white text-[13px] cursor-pointer flex items-center gap-2">
            <i class="fa-solid fa-file text-xs"></i>Upload files
            <form action="{{ route('ocr.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="images[]" class="hidden" multiple
                    onchange="this.form.submit()"
                    accept=".jpg,.jpeg,.png,.bmp,.gif,.tif,.tiff,.webp,.pdf">
            </form>
        </label>
    </div>
</header>
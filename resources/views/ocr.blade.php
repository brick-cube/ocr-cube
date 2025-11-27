<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Multi Receipts OCR Parsing</title>

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Inter font + FontAwesome --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
    </style>
</head>

<body class="bg-white text-[#575656] min-h-screen flex flex-col">

    @include('components.ocr.header')

    <main class="flex flex-1">
        @include('components.ocr.sidebar')

        @include('components.ocr.viewer')

        <section class="flex-1 px-5 border-l border-[#f1f1f1]">
            @include('components.ocr.fields', ['extractedFields' => $extractedFields ?? []])
            @include('components.ocr.line-items', ['lineItems' => $lineItems ?? []])
            @include('components.ocr.raw')
        </section>
    </main>
    <script>
        let zoomLevel = 1;
        let dragEnabled = false; // Only enabled after double click
        const img = document.getElementById("preview");
        const fill = document.getElementById("zoomFill");
        const container = document.getElementById("imageContainer");

        let isDragging = false;
        let startX, startY, initialX = 0,
            initialY = 0;

        function updateZoomBar() {
            fill.style.width = (zoomLevel * 25) + "%";
        }

        if (img) {

            // Double click to enable drag mode
            container.addEventListener("dblclick", () => {
                if (zoomLevel <= 1) return; // Only when zoomed

                dragEnabled = !dragEnabled; // toggle on/off

                container.style.cursor = dragEnabled ? "grab" : "default";
            });

            // ZOOM IN
            document.getElementById("zoomInBtn").addEventListener("click", () => {
                zoomLevel = Math.min(zoomLevel + 0.25, 4);
                img.style.transform = `scale(${zoomLevel}) translate(${initialX}px, ${initialY}px)`;
                updateZoomBar();
            });

            // ZOOM OUT
            document.getElementById("zoomOutBtn").addEventListener("click", () => {
                zoomLevel = Math.max(zoomLevel - 0.25, 1);
                initialX = 0;
                initialY = 0;
                dragEnabled = false;
                container.style.cursor = "default";
                img.style.transform = `scale(${zoomLevel}) translate(0px, 0px)`;
                updateZoomBar();
            });

            // RESET
            document.getElementById("resetZoomBtn").addEventListener("click", () => {
                zoomLevel = 1;
                initialX = 0;
                initialY = 0;
                dragEnabled = false;
                container.style.cursor = "default";
                img.style.transform = `scale(1) translate(0px, 0px)`;
                updateZoomBar();
            });

            // DRAG START
            container.addEventListener("mousedown", e => {
                if (!dragEnabled || zoomLevel <= 1) return;
                isDragging = true;
                container.style.cursor = "grabbing";
                startX = e.clientX - initialX;
                startY = e.clientY - initialY;
            });

            // DRAGGING
            container.addEventListener("mousemove", e => {
                if (!isDragging) return;
                e.preventDefault();
                initialX = e.clientX - startX;
                initialY = e.clientY - startY;
                img.style.transform = `scale(${zoomLevel}) translate(${initialX}px, ${initialY}px)`;
            });

            // DRAG END
            function stopDragging() {
                isDragging = false;
                if (dragEnabled) container.style.cursor = "grab";
            }

            container.addEventListener("mouseup", stopDragging);
            container.addEventListener("mouseleave", stopDragging);
        }
    </script>


</body>

</html>
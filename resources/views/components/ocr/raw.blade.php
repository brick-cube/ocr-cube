@if(isset($raw))
<details class="mt-3 max-w-sm text-xs text-gray-500">
    <summary class="cursor-pointer">Raw Response</summary>
    <pre class="mt-1 max-h-48 overflow-auto bg-gray-100 text-[11px] p-2 rounded">
        {{ json_encode($raw, JSON_PRETTY_PRINT) }}
    </pre>
</details>
@endif

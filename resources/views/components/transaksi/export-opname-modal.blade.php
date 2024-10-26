<x-modal.modal-export title="Cetak & Konversi Stok Opname">
    <form method="POST" target="_blank"
        action="{{ route('stokopname.export') }}?{{ http_build_query(request()->only(['search', 'sort_by', 'direction', 'gudang'])) }}"
        class="p-4 md:p-5"">
        @csrf
        <div class="grid gap-4 mb-4">
            <x-modal.format-export-selection></x-modal>
        </div>
        <x-modal.button-export-selection></x-modal>
    </form>
</x-modal.modal-export>

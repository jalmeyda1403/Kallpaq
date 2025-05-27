<div wire:ignore.self class="modal fade" id="pdfModal" tabindex="-1" role="dialog" aria-labelledby="pdfModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="pdfModalLabel">Vista previa PDF</h5>
                <button type="button" id= "closepdfModal" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <div wire:loading class="loading-spinner">
                    <div class="spinner-border text-danger" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>

                @if ($pdfUrl)
                    @if ($esExterno)
                        <iframe src="{{ $pdfUrl }}" width="100%" height="600px" frameborder="0"
                            wire:loading.remove></iframe>
                    @else
                        <iframe src="{{ route('documentos.mostrar', ['path' => urlencode($pdfUrl)]) }}" width="100%"
                            height="600px" frameborder="0" wire:loading.remove></iframe>
                    @endif
                @endif
               
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        // Opcional: limpiar URL al cerrar modal
        $('#pdfModal').on('hidden.bs.modal', function() {
            @this.call('resetPdfUrl');
        });
    </script>
@endpush

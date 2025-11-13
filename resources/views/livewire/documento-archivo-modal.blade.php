<div wire:ignore.self class="modal fade" id="archivoModal" tabindex="-1" role="dialog"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 95vw; height: 95vh;">
        <div class="modal-content" style="height: 100%;">
            <div class="modal-header bg-danger text-white">
                <h6 class="modal-title">{{ $modalTitle }}</h6>
                <button type="button" class="close text-white" aria-label="Close" id="closeArchivoModal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-0" style="height: calc(100% - 56px);">
                @if ($archivo_path)
                    @if ($esExterno)
                        <iframe src="{{ $archivo_path }}" width="100%" height="100%" style="border: none;"></iframe>
                    @else
                        <iframe src="{{ route('documentos.mostrar', ['url' => urlencode($archivo_path)]) }}"
                                width="100%" height="100%" style="border: none;"></iframe>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script>
        $('#closeArchivoModal').on('click', function() {
            $('#archivoModal').modal('hide');
        });
    
       
    </script>
@endpush

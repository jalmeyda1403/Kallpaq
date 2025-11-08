<div wire:ignore.self class="modal fade" id="documentoHijosModal" tabindex="-1" role="dialog" aria-hidden="true"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger">
                <h6 class="modal-title">Documentos Relacionados</h6>
                <button type="button" class="close" aria-label="Close" id="closeSearcHijoModal">
                    <span aria-hidden="true" style="color: white">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div wire:loading class="loading-spinner">
                    <div class="spinner-border text-dark" role="status">
                        <span class="sr-only">Cargando...</span>
                    </div>
                </div>
                <table class="table table-bordered table-hover table-versiones table-sm">
                    <thead class="table-header bg-dark">
                        <tr>
                            <th class="text-center">Código</th>
                            <th class="text-center">Tipo</th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Versión</th>
                            <th class="text-center">Fecha Vigencia</th>
                            <th class="text-center">Enlace</th>
           
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documentosHijos as $doc)
                            @php
                                $version = $doc->versiones->first();
                            @endphp
                            <tr class="border">
                                <td class="text-center"> {{ $doc->cod_documento }}</td>
                                <td class="text-center">{{ $doc->tipo_documento->nombre }}</td>
                                <td > {{ $doc->nombre }}</td>
                                <td class="text-center">   {{ str_pad($doc->ultimaVersion->version?? '0', 2, '0', STR_PAD_LEFT) }}</td>
                             
                                <td class="text-center text-nowrap">
                                    {{ $doc->ultimaVersion->fecha_vigencia ?? '' }}
                                </td>
                                @php
                                    $archivoPath = optional($doc->ultimaVersion)->archivo_path ?? '';
                                    $extension = pathinfo($archivoPath, PATHINFO_EXTENSION);
                                @endphp
                                <td class="text-center">
                                    @if (strtolower($extension) === 'pdf')
                                        <a href="#" class="px-1 btnVerDocumento" data-toggle="modal"
                                            onclick="Livewire.dispatchTo('pdf-modal','openPdfModal', { path: '{{ optional($doc->ultimaVersion)->archivo_path ?? '' }}' })"
                                            data-target="#pdfModal" title="Abrir Pdf">
                                            <i class="fas fa-file-pdf fa-lg text-danger"></i>
                                        </a>
                                    @else
                                        {{-- Otro tipo de archivo, solo descarga --}}
                                        <a href="{{ asset('storage/' . $archivoPath) }}" target="_blank"
                                            title="Descargar Archivo">
                                            <i class="fas fa-download fa-lg text-secondary"></i>
                                        </a>
                                    @endif

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-2">No hay documentos hijos</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>


            </div>
        </div>

    </div>
</div>
@push('scripts')
    <script>
        $('#closeSearcHijoModal').on('click', function() {
            $('#documentoHijosModal').modal('hide');
        });
    </script>
@endpush

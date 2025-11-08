@push('styles')
    <style>
        .summary-card {
            border-radius: 5px;
            padding: 1rem;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 120px;
            text-align: center;
            z-index: 0.8;
        }

       

        .summary-card i {
            font-size: 1.5rem;
            margin-bottom: 0.2rem;
        }

        .summary-card h4 {
            margin: 0;
            font-size: 1.6rem;
        }

        .summary-card p {
            margin: 0;
            font-size: 0.9rem;
        }
    </style>
@endpush


<div class="row text-white">
    <div class="col-md-2 col-sm-6 mb-3">
        <div class="summary-card bg-dark text-white rounded shadow p-3 text-center">
            <div class="mb-2">
                <i class="fas fa-tasks fa-2x"></i>
            </div>
            <div class="d-flex justify-content-around align-items-center">
                <div>
                    <h4 class="mb-0">{{ $total }} |</h4>
                    <small>Total | </small>
                </div>
                <div>
                    <h4 class="mb-0">{{ $sin_asignar }}</h4>
                    <small>Sin asignar</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="summary-card bg-secondary">
            <i class="fas fa-ban"></i>
            <h4>{{ $desestimados }}</h4>
            <div class="small-box-footer">
                Desestimados</div>
        </div>
    </div>


    <div class="col-md-2">
        <div class="summary-card bg-green">
            <i class="fas fa-spinner"></i>
            <h4>{{ $enProceso }}</h4>
            <div class="small-box-footer">En Proceso</div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="summary-card bg-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <h4>{{ $vencidos }}</h4>
            <div class="small-box-footer">Vencidos</div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="summary-card bg-primary">
            <i class="fas fa-check-circle"></i>
            <h4>{{ $finalizados }}</h4>
            <div class="small-box-footer">Finalizados</div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="summary-card bg-purple">
            <i class="fas fa-check-double"></i>
            <h4>{{ $eficacia }}%</h4>
            <p>Eficacia</p>
        </div>
    </div>
    <hr>

</div>

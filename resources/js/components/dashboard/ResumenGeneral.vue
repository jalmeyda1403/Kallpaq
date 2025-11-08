<template>
  <div class="row text-white">
    <div class="col-md-2 col-sm-6 mb-3">
      <div class="summary-card bg-dark text-white rounded shadow p-3 text-center">
        <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></div>
        <div v-else>
          <div class="mb-2">
            <i class="fas fa-tasks fa-2x"></i>
          </div>
          <div class="d-flex justify-content-around align-items-center">
            <div>
              <h4 class="mb-0">{{ stats.total }} |</h4>
              <small>Total | </small>
            </div>
            <div>
              <h4 class="mb-0">{{ stats.sin_asignar }}</h4>
              <small>Sin asignar</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="summary-card bg-secondary">
        <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></div>
        <div v-else>
          <i class="fas fa-ban"></i>
          <h4>{{ stats.desestimados }}</h4>
          <div class="small-box-footer">Desestimados</div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="summary-card bg-green">
        <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></div>
        <div v-else>
          <i class="fas fa-spinner"></i>
          <h4>{{ stats.enProceso }}</h4>
          <div class="small-box-footer">En Proceso</div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="summary-card bg-danger">
        <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></div>
        <div v-else>
          <i class="fas fa-exclamation-triangle"></i>
          <h4>{{ stats.vencidos }}</h4>
          <div class="small-box-footer">Vencidos</div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="summary-card bg-primary">
        <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></div>
        <div v-else>
          <i class="fas fa-check-circle"></i>
          <h4>{{ stats.finalizados }}</h4>
          <div class="small-box-footer">Finalizados</div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="summary-card bg-purple">
        <div v-if="isLoading" class="spinner-border spinner-border-sm" role="status"></div>
        <div v-else>
          <i class="fas fa-check-double"></i>
          <h4>{{ stats.eficacia }}%</h4>
          <p>Eficacia</p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
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

<script>
import axios from 'axios';
import { route } from 'ziggy-js';
import { Ziggy } from '../../ziggy';

export default {
  data() {
    return {
      isLoading: false,
      stats: {
        total: 0,
        sin_asignar: 0,
        desestimados: 0,
        enProceso: 0,
        vencidos: 0,
        finalizados: 0,
        eficacia: 0,
      },
    };
  },
  mounted() {
    this.isLoading = true;
      axios.get(route('dashboard.resumenGeneral', {}, false, Ziggy))
        .then(response => {
          this.stats = response.data;
        })
        .catch(error => {
          console.error('Error al cargar el resumen general:', error);
        })
        .finally(() => {
          this.isLoading = false;
        });
  },
};
</script>

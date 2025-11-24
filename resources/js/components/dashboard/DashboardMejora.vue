<template>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12 mt-4">
        <hallazgo-resumen-general
          :mostrar-todos="mostrarTodos"
          :es-admin="esAdmin"
          @update:mostrar-todos="mostrarTodos = $event"
        ></hallazgo-resumen-general>
      </div>

      <div class="col-md-6">
        <div class="h-100 d-flex flex-column">
          <hallazgo-resumen-grafico
            :mostrar-todos="mostrarTodos"
            :es-admin="esAdmin"
          ></hallazgo-resumen-grafico>
        </div>
      </div>
      <div class="col-md-6">
        <div class="h-100 d-flex flex-column">
          <hallazgo-resumen-alertas
            :mostrar-todos="mostrarTodos"
            :es-admin="esAdmin"
          ></hallazgo-resumen-alertas>
        </div>
      </div>

      <div class="col-md-12 mt-4">
        <hallazgo-resumen-procesos
          :mostrar-todos="mostrarTodos"
          :es-admin="esAdmin"
        ></hallazgo-resumen-procesos>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import HallazgoResumenGeneral from '@/components/dashboard/HallazgoResumenGeneral.vue';
import HallazgoResumenGrafico from '@/components/dashboard/HallazgoResumenGrafico.vue';
import HallazgoResumenAlertas from '@/components/dashboard/HallazgoResumenAlertas.vue';
import HallazgoResumenProcesos from '@/components/dashboard/HallazgoResumenProcesos.vue';

export default {
  name: 'DashboardMejora',
  components: {
    HallazgoResumenGeneral,
    HallazgoResumenGrafico,
    HallazgoResumenAlertas,
    HallazgoResumenProcesos,
  },
  data() {
    return {
      mostrarTodos: false, // Control central para mostrar todos los hallazgos (solo para admins)
      esAdmin: false,
    };
  },
  mounted() {
    // Determinar si el usuario es administrador (podríamos obtenerlo del store global)
    // Por ahora lo dejamos como falso o lo determinamos desde la sesión
    this.verificarRolUsuario();
  },
  methods: {
    async verificarRolUsuario() {
      // Con Sanctum, la información del usuario puede estar en window.App o se puede obtener desde una API
      if (typeof window.App !== 'undefined' && window.App.user) {
        // Verificar si el usuario tiene el rol de admin
        if (window.App.user.roles) {
          // Si el backend devuelve roles como un array
          this.esAdmin = Array.isArray(window.App.user.roles)
            ? window.App.user.roles.includes('admin')
            : window.App.user.roles === 'admin';
        } else if (window.App.user.role) {
          // Si el backend devuelve el rol como una cadena
          this.esAdmin = window.App.user.role === 'admin';
        }
      } else {
        // Intentar obtener la información de usuario desde la API de Sanctum
        try {
          const response = await axios.get('/api/user');
          const userData = response.data;
          if (userData.roles) {
            this.esAdmin = Array.isArray(userData.roles)
              ? userData.roles.includes('admin')
              : userData.roles === 'admin';
          } else if (userData.role) {
            this.esAdmin = userData.role === 'admin';
          }
        } catch (error) {
          console.error('Error obteniendo la información del usuario:', error);
          this.esAdmin = false;
        }
      }
    }
  }
};
</script>

<style scoped>
/* Si tienes estilos específicos para esta página, puedes ponerlos aquí */
</style>
<template>
  <div>
    <h4>Solicitudes de Mejora</h4>
    <p>Aquí se gestionarán las solicitudes de mejora para el proceso con ID: {{ procesoId }}</p>
    </div>
</template>

<script>
export default {
  // Aquí se definen las propiedades que recibe del componente padre
  props: {
    procesoId: {
      type: [Number, String],
      required: true
    }
  },
  // Aquí puedes añadir la lógica para cargar los datos cuando el componente se monte
  mounted() {
    console.log('Componente Solicitudes de Mejora montado.');
    // Si idProceso no es nulo, puedes cargar los datos aquí
    if (this.procesoId) {
      // Lógica para cargar las solicitudes de mejora
    }
  }
}
</script>

<style scoped>
/* Estilos específicos para este componente */
</style>
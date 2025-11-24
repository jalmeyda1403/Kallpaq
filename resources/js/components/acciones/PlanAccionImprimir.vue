<template>
  <div class="plan-accion-imprimir">
    <!-- Encabezado del documento -->
    <div class="encabezado mb-4">
      <div class="row">
        <div class="col-3 text-center">
          <img :src="logoSrc" alt="Logo" class="logo">
        </div>
        <div class="col-6 text-center">
          <h3 class="titulo-documento mb-1">PLAN DE ACCIÓN</h3>
          <h5 class="titulo-documento">HALLAZGO: {{ hallazgo.hallazgo_cod }}</h5>
        </div>
        <div class="col-3 text-right">
          <div class="codigo-version">
            <p class="mb-1"><strong>Fecha:</strong> {{ fechaActual }}</p>
          </div>
        </div>
      </div>
      <hr class="linea-separadora">
    </div>

    <!-- Información del hallazgo -->
    <div class="info-hallazgo mb-4">
      <div class="datos-generales mb-3">
        <div class="row">
          <div class="col-md-8">
            <p class="mb-1"><strong>Resumen:</strong> {{ hallazgo.hallazgo_resumen }}</p>
            <p class="mb-1"><strong>Descripción:</strong> {{ hallazgo.hallazgo_descripcion }}</p>
          </div>
          <div class="col-md-4">
            <p class="mb-1"><strong>Clasificación:</strong> 
              <span>{{ hallazgo.hallazgo_clasificacion }}</span>
            </p>
            <p class="mb-1"><strong>Estado:</strong> {{ hallazgo.hallazgo_estado }}</p>
            <p class="mb-1"><strong>Fecha Identificación:</strong> {{ formatDate(hallazgo.hallazgo_fecha_identificacion) }}</p>
          </div>
        </div>
      </div>

      <div class="procesos-afectados mb-3">
        <h5 class="subtitulo mb-2">Procesos Afectados:</h5>
        <div class="procesos-lista">
          <span v-for="(proceso, index) in hallazgo.procesos" :key="proceso.id" class="proceso-tag mr-2">
            {{ proceso.proceso_nombre }}<span v-if="index < hallazgo.procesos.length - 1">,</span>
          </span>
        </div>
      </div>
    </div>

    <!-- Análisis de causa raíz -->
    <div class="causa-raiz mb-4" v-if="causaRaiz && (hallazgo.hallazgo_clasificacion === 'NCM' || hallazgo.hallazgo_clasificacion === 'Ncme')">
      <h5 class="subtitulo mb-2">Análisis de Causa Raíz:</h5>
      <div class="causa-detalle p-3 border rounded">
        <p class="mb-1"><strong>Método aplicado:</strong> {{ getMetodoLabel(causaRaiz.causa_metodo) }}</p>

        <!-- 5 Porqués -->
        <div v-if="causaRaiz.causa_metodo === 'cinco_porques'" class="mb-3">
          <div v-for="i in 5" :key="i" class="mb-1">
            <strong>¿Por qué {{ i }}?:</strong>
            {{ causaRaiz[`causa_por_que${i}`] || 'No especificado' }}
          </div>
        </div>

        <!-- Ishikawa (6M) -->
        <div v-else-if="causaRaiz.causa_metodo === 'ishikawa'" class="mb-3">
          <div class="row">
            <div class="col-md-6">
              <p class="mb-1"><strong>Mano de Obra:</strong> {{ causaRaiz.causa_mano_obra || 'No especificado' }}</p>
              <p class="mb-1"><strong>Metodologías:</strong> {{ causaRaiz.causa_metodologias || 'No especificado' }}</p>
              <p class="mb-1"><strong>Materiales:</strong> {{ causaRaiz.causa_materiales || 'No especificado' }}</p>
            </div>
            <div class="col-md-6">
              <p class="mb-1"><strong>Máquinas:</strong> {{ causaRaiz.causa_maquinas || 'No especificado' }}</p>
              <p class="mb-1"><strong>Medición:</strong> {{ causaRaiz.causa_medicion || 'No especificado' }}</p>
              <p class="mb-1"><strong>Medio Ambiente:</strong> {{ causaRaiz.causa_medio_ambiente || 'No especificado' }}</p>
            </div>
          </div>
        </div>

        <p class="mb-0"><strong>Causa Raíz Final:</strong> {{ causaRaiz.causa_resultado }}</p>
      </div>
    </div>

    <!-- Tabla de acciones -->
    <div class="acciones-listado mb-4">
      <h5 class="subtitulo mb-3">Acciones del Plan</h5>
      <table class="table table-bordered">
        <thead class="thead-light">
          <tr>
            <th style="width: 10%;">Código</th>
            <th style="width: 10%;">Tipo</th>
            <th style="width: 25%;">Descripción</th>
            <th style="width: 15%;">Responsable</th>
            <th style="width: 10%;">Inicio</th>
            <th style="width: 10%;">Fin Planif.</th>
            <th style="width: 10%;">Estado</th>
            <th style="width: 10%;">Ciclo</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="accion in acciones" :key="accion.id">
            <td class="codigo">{{ accion.accion_cod }}</td>
            <td>
              {{ getTipoAccionLabel(accion.accion_tipo) }}
            </td>
            <td class="descripcion">{{ accion.accion_descripcion }}</td>
            <td class="responsable">{{ accion.accion_responsable }}</td>
            <td class="fecha">{{ formatDate(accion.accion_fecha_inicio) }}</td>
            <td :class="{ 'text-danger font-weight-bold': isFechaVencida(accion.accion_fecha_fin_planificada) }">
              {{ formatDate(accion.accion_fecha_fin_planificada) }}
            </td>
            <td>
              {{ accion.accion_estado }}
            </td>
            <td class="ciclo">{{ accion.accion_ciclo }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Espacio para firma -->
    <div class="firma-section mt-5 pt-4">
      <div class="row mt-4">
        <div class="col-12 text-center">
          <div class="firma-linea" style="border-top: 1px solid #000; width: 300px; margin: 40px auto 10px auto;"></div>
          <p class="mb-0"><strong>Firma del Propietario del Proceso</strong></p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useHallazgoStore } from '@/stores/hallazgoStore';
import { storeToRefs } from 'pinia';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

const props = defineProps({
  hallazgoId: {
    type: [Number, String],
    required: true
  }
});

const hallazgoStore = useHallazgoStore();
const { hallazgoForm: hallazgo, causaRaiz: causaRaizOriginal, todasLasAcciones: acciones } = storeToRefs(hallazgoStore);

// Referencia local para causaRaiz
const causaRaiz = computed(() => causaRaizOriginal.value);

// Variables reactivas
const fechaActual = ref('');
const logoSrc = ref('/images/logo.png');

// Calcular estadísticas
const accionesFinalizadas = computed(() => {
  return acciones.value ? acciones.value.filter(accion => accion.accion_estado === 'finalizada').length : 0;
});

const porcentajeAvance = computed(() => {
  if (!acciones.value || acciones.value.length === 0) return 0;
  const finalizadas = accionesFinalizadas.value;
  return Math.round((finalizadas / acciones.value.length) * 100);
});

// Métodos auxiliares
const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
};

const getMetodoLabel = (metodo) => {
  const labels = {
    'cinco_porques': '5 Porqués',
    'ishikawa': 'Ishikawa (6M)'
  };
  return labels[metodo] || metodo;
};


const getTipoAccionLabel = (tipo) => {
  const labels = {
    'inmediata': 'Inmediata',
    'correctiva': 'Correctiva'
  };
  return labels[tipo] || tipo;
};


const isFechaVencida = (fecha) => {
  if (!fecha) return false;
  const fechaFin = new Date(fecha);
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  fechaFin.setHours(0, 0, 0, 0);
  return fechaFin < hoy;
};

const generarPDF = async () => {
  // Esperar a que el DOM se haya completamente renderizado
  await new Promise(resolve => setTimeout(resolve, 1500));

  const element = document.querySelector('.plan-accion-imprimir');

  if (element) {
    // Usar html2canvas para capturar el contenido
    const canvas = await html2canvas(element, {
      scale: 2, // Escala original para mejor calidad
      useCORS: true, // Para manejar imágenes de otros dominios
      logging: false,
      backgroundColor: '#ffffff' // Asegurar fondo blanco
    });

    // Comprimir la imagen antes de agregarla al PDF (sin reducir tanto la calidad)
    const imgData = await compressImage(canvas.toDataURL('image/jpeg', 0.85));

    const pdf = new jsPDF('p', 'mm', 'a4');
    const imgWidth = 210; // Ancho A4 en mm
    const pageHeight = 297; // Alto A4 en mm
    const imgHeight = (canvas.height * imgWidth) / canvas.width;
    let heightLeft = imgHeight;
    let position = 0;

    pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight, undefined, 'SLOW'); // Mejor calidad
    heightLeft -= pageHeight;

    // Si el contenido es más largo que una página, añadir más páginas
    while (heightLeft >= 0) {
      position = heightLeft - imgHeight;
      pdf.addPage();
      pdf.addImage(imgData, 'JPEG', 0, position, imgWidth, imgHeight, undefined, 'SLOW');
      heightLeft -= pageHeight;
    }

    // Descargar el PDF generado
    pdf.save(`Plan_de_Accion_${hallazgo.value.hallazgo_cod || props.hallazgoId}.pdf`);
  }
};

// Función para comprimir la imagen
const compressImage = (base64String) => {
  return new Promise((resolve) => {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();

    img.onload = () => {
      // Calcular nuevas dimensiones para reducir el tamaño
      const maxWidth = 1600; // Aumentar el ancho máximo para mejor calidad
      let width = img.width;
      let height = img.height;

      if (width > maxWidth) {
        height = (img.height * maxWidth) / img.width;
        width = maxWidth;
      }

      canvas.width = width;
      canvas.height = height;

      ctx.drawImage(img, 0, 0, width, height);

      // Comprimir la imagen a JPEG con calidad más alta
      const compressedBase64 = canvas.toDataURL('image/jpeg', 0.85);
      resolve(compressedBase64);
    };

    img.src = base64String;
  });
};

onMounted(async () => {
  // Establecer la fecha actual
  const hoy = new Date();
  const opciones = { year: 'numeric', month: '2-digit', day: '2-digit' };
  fechaActual.value = hoy.toLocaleDateString('es-ES', opciones);

  // Cargar los datos del plan de acción
  await Promise.all([
    hallazgoStore.fetchHallazgo(props.hallazgoId),
    hallazgoStore.fetchTodasLasAcciones(props.hallazgoId),
    hallazgoStore.fetchCausaRaiz(props.hallazgoId)
  ]);

  // Generar el PDF después de que los datos se hayan cargado
  await generarPDF();
});
</script>

<style scoped>
.plan-accion-imprimir {
  font-family: 'Arial', 'Helvetica', sans-serif;
  font-size: 11px;
  line-height: 1.6;
  color: #000;
  padding: 20px;
  background-color: #fff;
  max-width: 210mm; /* Ancho A4 */
  margin: 0 auto;
  min-height: 297mm; /* Alto A4 */
}

.logo {
  max-width: 150px;
  height: auto;
  margin: 10px;
}

.titulo-documento {
  color: #dc3545;
  margin: 0;
  font-weight: bold;
  text-transform: uppercase;
  font-size: 14px;
}

.linea-separadora {
  border-top: 2px solid #dc3545;
  margin: 15px 0;
}

.subtitulo {
  background-color: #f8f9fa;
  padding: 12px 15px;
  border-left: 4px solid #dc3545;
  margin: 0 0 20px 0;
  font-weight: bold;
  color: #495057;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 12px;
}

.codigo-version {
  background-color: #e9ecef;
  padding: 10px;
  border-radius: 4px;
  margin-top: 10px;
}

.proceso-tag {
  background-color: #e9ecef;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 11px;
}

.causa-detalle {
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
}

.table {
  font-size: 10px;
  margin-bottom: 0;
  border-collapse: collapse;
}

.table th,
.table td {
  padding: 8px;
  vertical-align: middle;
  border: 1px solid #dee2e6;
}

.descripcion {
  min-width: 150px;
}

.thead-light th {
  background-color: #f8f9fa;
  color: #495057;
  border: 1px solid #dee2e6;
  font-weight: bold;
  text-align: center;
  font-size: 12px;
}



.estadistica-card {
  border: 1px solid #ced4da;
  border-radius: 8px;
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.display-4 {
  font-size: 2.5rem;
  font-weight: 300;
  line-height: 1.2;
  margin: 0;
  color: #dc3545;
}

.firma-area {
  border-top: 1px solid #000;
  padding-top: 20px;
  margin-top: 40px;
  margin-bottom: 10px;
}

/* Estilos para impresión */
@media print {
  @page {
    size: A4;
    margin: 20mm;
  }

  body {
    background-color: #fff;
  }

  .plan-accion-imprimir {
    box-shadow: none;
    margin: 0;
    padding: 0;
    max-width: 100%;
  }

  /* Ocultar elementos que no se deben imprimir */
  .no-print {
    display: none !important;
  }
}
</style>
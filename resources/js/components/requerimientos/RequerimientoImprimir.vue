<template>
  <div class="requerimiento-imprimir">
    <!-- Encabezado del documento -->
    <div class="encabezado mb-4">
      <div class="row">
        <div class="col-3 text-center">
          <img :src="logoSrc" alt="Logo" class="logo">
        </div>
        <div class="col-6 text-center">
          <h3 class="titulo-documento mb-1">FICHA DE REQUERIMIENTO</h3>
          <h5 class="titulo-documento">RQ - {{ str_pad(requerimiento.id, 3, '0', '0') }}</h5>
        </div>
        <div class="col-3 text-right">
          <div class="codigo-version">
            <p class="mb-1"><strong>Fecha:</strong> {{ fechaActual }}</p>
          </div>
        </div>
      </div>
      <hr class="linea-separadora">
    </div>

    <!-- Información General -->
    <div class="section mb-4">
      <div class="section-title">1. INFORMACIÓN GENERAL DEL REQUERIMIENTO</div>
      <table class="content-table">
        <tbody>
          <tr>
            <th>Proceso</th>
            <td>{{ requerimiento.proceso.proceso_nombre ?? 'N/A' }} ({{ requerimiento.proceso.cod_proceso ?? 'N/A' }})</td>
          </tr>
          <tr>
            <th>Asunto del Requerimiento</th>
            <td>{{ requerimiento.asunto }}</td>
          </tr>
          <tr>
            <th>Descripción</th>
            <td>{{ requerimiento.descripcion }}</td>
          </tr>
          <tr>
            <th>Justificación</th>
            <td>{{ requerimiento.justificacion }}</td>
          </tr>
          <tr>
            <th>Solicitante</th>
            <td>{{ requerimiento.solicitante.name ?? 'N/A' }}</td>
          </tr>
          <tr>
            <th>Fecha de Creación</th>
            <td>{{ formatDate(requerimiento.created_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Evaluación de Complejidad -->
    <div class="section mb-4" v-if="requerimiento.evaluacion">
      <div class="section-title">2. EVALUACIÓN PRELIMINAR DE COMPLEJIDAD</div>
      <table class="content-table evaluation-table">
        <thead>
          <tr>
            <th style="width: 80%;">Criterio Evaluado</th>
            <th style="width: 20%;">Puntaje</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <strong>Cantidad de actividades del proceso</strong>
              <div style="font-size: 10px; color: #555; margin-top: 5px;">
                {{ evaluacionOptions.actividades[requerimiento.evaluacion.num_actividades] ?? 'N/A' }}
              </div>
            </td>
            <td>{{ requerimiento.evaluacion.num_actividades }}</td>
          </tr>
          <tr>
            <td>
              <strong>Cantidad de unidades orgánicas involucradas</strong>
              <div style="font-size: 10px; color: #555; margin-top: 5px;">
                {{ evaluacionOptions.areas[requerimiento.evaluacion.num_areas] ?? 'N/A' }}
              </div>
            </td>
            <td>{{ requerimiento.evaluacion.num_areas }}</td>
          </tr>
          <tr>
            <td>
              <strong>Requisitos normativos aplicables</strong>
              <div style="font-size: 10px; color: #555; margin-top: 5px;">
                {{ evaluacionOptions.requisitos[requerimiento.evaluacion.num_requisitos] ?? 'N/A' }}
              </div>
            </td>
            <td>{{ requerimiento.evaluacion.num_requisitos }}</td>
          </tr>
          <tr>
            <td>
              <strong>Nivel de documentación requerida</strong>
              <div style="font-size: 10px; color: #555; margin-top: 5px;">
                {{ evaluacionOptions.documentacion[requerimiento.evaluacion.nivel_documentacion] ?? 'N/A' }}
              </div>
            </td>
            <td>{{ requerimiento.evaluacion.nivel_documentacion }}</td>
          </tr>
          <tr>
            <td>
              <strong>Impacto del procedimiento</strong>
              <div style="font-size: 10px; color: #555; margin-top: 5px;">
                {{ evaluacionOptions.impacto[requerimiento.evaluacion.impacto_requerimiento] ?? 'N/A' }}
              </div>
            </td>
            <td>{{ requerimiento.evaluacion.impacto_requerimiento }}</td>
          </tr>
          <tr>
            <th style="text-align: right;">PUNTAJE TOTAL</th>
            <td>{{ requerimiento.evaluacion.complejidad_valor }}</td>
          </tr>
          <tr>
            <th style="text-align: right;">NIVEL DE COMPLEJIDAD</th>
            <td>{{ requerimiento.evaluacion.complejidad_nivel }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Espacio para firma -->
    <div class="footer mt-5 pt-4">
      <div class="signature-line text-center" style="width: 300px; margin: 40px auto 10px auto; border-top: 1px solid #000;">
        <p class="mb-0"><strong>Firma del Propietario del Proceso</strong></p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRequerimientoStore } from '@/stores/requerimientoStore'; // Asumiendo que tienes un store para requerimientos
import { storeToRefs } from 'pinia';
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';

const props = defineProps({
  requerimientoId: {
    type: [Number, String],
    required: true
  }
});

// Asumiendo que tienes un store de pinia para requerimientos
const requerimientoStore = useRequerimientoStore();
const { requerimientoForm: requerimiento } = storeToRefs(requerimientoStore);

// Variables reactivas
const fechaActual = ref('');
const logoSrc = ref('/images/logo.png');

// Opciones de evaluación (deberías obtenerlas desde tu backend o helper)
const evaluacionOptions = {
  actividades: {
    1: 'Menos de 5 actividades',
    2: 'Entre 5 y 10 actividades',
    3: 'Más de 10 actividades'
  },
  areas: {
    1: '1 área involucrada',
    2: '2-3 áreas involucradas',
    3: 'Más de 3 áreas involucradas'
  },
  requisitos: {
    1: 'Pocos requisitos aplicables',
    2: 'Algunos requisitos aplicables',
    3: 'Muchos requisitos aplicables'
  },
  documentacion: {
    1: 'Baja documentación requerida',
    2: 'Documentación media requerida',
    3: 'Alta documentación requerida'
  },
  impacto: {
    1: 'Bajo impacto',
    2: 'Impacto medio',
    3: 'Alto impacto'
  }
};

// Funciones auxiliares
const str_pad = (str, length, pad) => {
  str = str.toString();
  while (str.length < length) {
    str = pad + str;
  }
  return str;
};

const formatDate = (dateString) => {
  if (!dateString) return '';
  const date = new Date(dateString);
  const day = String(date.getDate()).padStart(2, '0');
  const month = String(date.getMonth() + 1).padStart(2, '0');
  const year = date.getFullYear();
  return `${day}/${month}/${year}`;
};

const generarPDF = async () => {
  // Esperar a que el DOM se haya completamente renderizado
  await new Promise(resolve => setTimeout(resolve, 1500));

  const element = document.querySelector('.requerimiento-imprimir');

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
    pdf.save(`Requerimiento_${requerimiento.value.id || props.requerimientoId}.pdf`);
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

  // Cargar los datos del requerimiento
  // Este store y método deben estar implementados en tu aplicación
  await requerimientoStore.fetchRequerimiento(props.requerimientoId);

  // Generar el PDF después de que los datos se hayan cargado
  await generarPDF();
});
</script>

<style scoped>
.requerimiento-imprimir {
  font-family: 'Arial', 'Helvetica', sans-serif;
  font-size: 11px;
  color: #000;
  line-height: 1.6;
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

.section-title {
  font-size: 12px;
  font-weight: bold;
  background-color: #f8f9fa;
  padding: 12px 15px;
  border-left: 4px solid #dc3545;
  margin-bottom: 20px;
  color: #495057;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.content-table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
  font-size: 11px;
}

.content-table th, .content-table td {
  border: 1px solid #dee2e6;
  padding: 10px;
  vertical-align: top;
}

.content-table th {
  background-color: #f8f9fa;
  font-weight: bold;
  width: 25%;
  text-align: left;
  color: #495057;
  font-size: 12px;
}

.content-table td {
  width: 75%;
  background-color: #fdfdfd;
}

.evaluation-table th {
  text-align: left;
}

.evaluation-table td:nth-child(2) {
  text-align: center;
  font-weight: bold;
  background-color: #f8f9fa;
}

.footer {
  margin-top: 50px;
  text-align: center;
}

.signature-line {
  width: 300px;
  margin: 40px auto 0 auto;
  padding-top: 5px;
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

  .requerimiento-imprimir {
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
</template>
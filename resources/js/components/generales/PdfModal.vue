
<template>
  <div class="modal fade" id="pdfModal" tabindex="-1" aria-labelledby="pdfModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="pdfModalLabel">Visor de PDF</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <iframe :src="pdfUrl" frameborder="0" width="100%" height="700px"></iframe>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Modal } from 'bootstrap';

export default {
  props: {
    initialPdfUrl: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      pdfUrl: this.initialPdfUrl,
      modalInstance: null
    };
  },
  methods: {
    openModal(pdfPath) {
      this.pdfUrl = pdfPath;
      if (this.modalInstance) {
          this.modalInstance.show();
      }
    }
  },
  mounted() {
    this.modalInstance = new Modal(document.getElementById('pdfModal'), {
      backdrop: 'static',
      keyboard: false
    });

    document.addEventListener('open-pdf-modal', (event) => {
      this.openModal(event.detail);
    });
  }
};
</script>

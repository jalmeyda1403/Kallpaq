<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class PdfModal extends Component
{
    public $pdfUrl = null;
    public $esExterno = false;

    protected $listeners = ['openPdfModal', 'resetPdfUrl'];

    public function resetPdfUrl()
    {
        $this->openPdfModal(null);
    }

    public function openPdfModal($path)
    {
        $this->pdfUrl = $path;
        $this->esExterno = filter_var($path, FILTER_VALIDATE_URL) ? true : false;

    }

    public function render()
    {
        return view('livewire.pdf-modal');
    }
}

<?php

namespace App\Service;

use Dompdf\Dompdf;

class PdfService
{
    private $domPdf;

    public function _construct() {
        $this->domPdf = new Dompdf();
    }

    public function showPdfFile($html){
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream("detailBilan.pdf" , [
            'Attachement'=>false
        ]);
    }

    public function generateBinaryPdf($html) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }

}
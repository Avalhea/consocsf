<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
//require_once 'dompdf/autoload.inc.php';
class PdfService
{
    private $domPdf;

    public function __construct() {
        $this->domPdf = new DomPdf();

//        $pdfOptions = new Options();
//
//        $pdfOptions->set('header','');
//        $this->domPdf->setOptions($pdfOptions);
    }

    public function showPdfFile($html, $nom) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream($nom, [
            'Attachement' => true
        ]);
    }

    public function generateBinaryPDF($html) {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->output();
    }
}
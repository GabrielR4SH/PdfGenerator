<?php
namespace PDFGeneratorOS;

require_once __DIR__ . '/../../vendor/autoload.php';

use TCPDF;

class PDF_generator {
    private $pdf;
    private $outputDir;
    private $base64Dir;
    private $imagesDir;

    public function __construct() {
        $this->outputDir = __DIR__ . '/../base64';
        $this->imagesDir = __DIR__ . '/../images';
        
        // Criar diretórios se não existirem
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0777, true);
        }
        
        if (!file_exists($this->imagesDir)) {
            mkdir($this->imagesDir, 0777, true);
        }
        
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }

    public function generateOS($data) {
        // Configurações do PDF
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor('PDFGeneratorOS');
        $this->pdf->SetTitle('Ordem de Serviço - ' . $data['cliente']);
        $this->pdf->SetSubject('Ordem de Serviço');
        
        // Adicionar uma página
        $this->pdf->AddPage();
        
        // Estilos
        $this->pdf->SetFont('helvetica', 'B', 16);
        $this->pdf->SetTextColor(33, 37, 41);
        
        // Cabeçalho
        $this->pdf->Cell(0, 10, 'ORDEM DE SERVIÇO', 0, 1, 'C');
        $this->pdf->Ln(10);
        
        // Informações da OS
        $this->pdf->SetFont('helvetica', '', 12);
        
        $html = '
        <style>
            .header { color: #2c3e50; font-weight: bold; }
            .value { color: #3498db; }
            .section { background-color: #f8f9fa; padding: 5px; margin-bottom: 10px; }
        </style>
        
        <div class="section">
            <span class="header">Cliente: </span>
            <span class="value">' . $data['cliente'] . '</span>
        </div>
        
        <div class="section">
            <span class="header">Serviço: </span>
            <span class="value">' . $data['servico'] . '</span>
        </div>
        
        <div class="section">
            <span class="header">Valor: </span>
            <span class="value">R$ ' . number_format($data['valor'], 2, ',', '.') . '</span>
        </div>
        
        <div class="section">
            <span class="header">Técnico Responsável: </span>
            <span class="value">' . $data['tecnico'] . '</span>
        </div>
        
        <div class="section">
            <span class="header">Data: </span>
            <span class="value">' . $data['data'] . '</span>
        </div>
        
        <div style="margin-top: 20px; text-align: center; color: #7f8c8d;">
            <p>Assinatura do Cliente</p>
            <p>________________________________________</p>
        </div>
        ';
        
        $this->pdf->writeHTML($html, true, false, true, false, '');
        
        // Gerar nome único para os arquivos
        $filename = 'OS_' . preg_replace('/[^a-z0-9]/i', '_', $data['cliente']) . '_' . date('YmdHis');
        $pdfPath = $this->imagesDir . '/' . $filename . '.pdf';
        $base64Path = $this->outputDir . '/' . $filename . '.txt';
        
        // Salvar PDF
        $this->pdf->Output($pdfPath, 'F');
        
        // Gerar e salvar Base64
        $pdfContent = file_get_contents($pdfPath);
        $base64Content = base64_encode($pdfContent);
        file_put_contents($base64Path, $base64Content);
        
        return [
            'pdf_path' => $pdfPath,
            'base64_path' => $base64Path
        ];
    }
}
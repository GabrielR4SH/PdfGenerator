<?php
namespace PDFGeneratorOS;

require_once __DIR__ . '/../vendor/autoload.php';

use FPDF;

class PDF_generator extends FPDF {
    private $outputDir;
    private $base64Dir;
    private $imagesDir;
    private $design;

    public function __construct() {
        parent::__construct('P', 'mm', 'A4');
        
        $this->outputDir = __DIR__ . '/../base64';
        $this->imagesDir = __DIR__ . '/../images';
        
        if (!file_exists($this->outputDir)) mkdir($this->outputDir, 0777, true);
        if (!file_exists($this->imagesDir)) mkdir($this->imagesDir, 0777, true);
        
        $this->design = $this->getRandomDesign();
    }

    private function getRandomDesign() {
        $designs = [
            [
                'primary' => [56, 142, 203],
                'secondary' => [231, 241, 248],
                'font' => 'Arial',
                'border' => 0
            ],
            [
                'primary' => [34, 139, 34],
                'secondary' => [237, 247, 237],
                'font' => 'Helvetica',
                'border' => 1
            ],
            [
                'primary' => [155, 89, 182],
                'secondary' => [245, 240, 247],
                'font' => 'Times',
                'border' => 'B'
            ],
            [
                'primary' => [192, 57, 43],
                'secondary' => [251, 238, 237],
                'font' => 'Courier',
                'border' => 0
            ]
        ];
        
        return $designs[array_rand($designs)];
    }

    public function Header() {
        // Cabeçalho colorido
        $this->SetFillColor($this->design['primary'][0], $this->design['primary'][1], $this->design['primary'][2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont($this->design['font'], 'B', 18);
        $this->Cell(0, 15, iconv('UTF-8', 'ISO-8859-1', 'ORDEM DE SERVIÇO'), $this->design['border'], 1, 'C', true);
        $this->Ln(5);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont($this->design['font'], 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    public function generateOS($data) {
        $this->AddPage();
        
        // Adicionando mais campos
        $data['numero_os'] = 'OS-' . date('Ymd') . rand(100, 999);
        $data['equipamento'] = readline("Equipamento: ");
        $data['defeito'] = readline("Defeito relatado: ");
        $data['laudo'] = readline("Laudo técnico: ");
        $data['prazo'] = readline("Prazo de entrega: ");
        $data['garantia'] = readline("Garantia: ");
        
        // Seção 1 - Dados da OS
        $this->SetFont($this->design['font'], 'B', 12);
        $this->SetFillColor($this->design['secondary'][0], $this->design['secondary'][1], $this->design['secondary'][2]);
        $this->Cell(0, 8, 'DADOS DA ORDEM DE SERVIÇO', $this->design['border'], 1, 'L', true);
        $this->Ln(2);
        
        $this->SetFont($this->design['font'], '', 10);
        $this->Cell(40, 7, 'Número OS:', 0, 0);
        $this->Cell(0, 7, $data['numero_os'], 0, 1);
        
        $this->Cell(40, 7, 'Data:', 0, 0);
        $this->Cell(0, 7, $data['data'], 0, 1);
        $this->Ln(5);
        
        // Seção 2 - Dados do Cliente
        $this->SetFont($this->design['font'], 'B', 12);
        $this->Cell(0, 8, 'DADOS DO CLIENTE', $this->design['border'], 1, 'L', true);
        $this->Ln(2);
        
        $this->SetFont($this->design['font'], '', 10);
        $this->Cell(40, 7, 'Cliente:', 0, 0);
        $this->Cell(0, 7, $data['cliente'], 0, 1);
        $this->Ln(5);
        
        // Seção 3 - Equipamento
        $this->SetFont($this->design['font'], 'B', 12);
        $this->Cell(0, 8, 'EQUIPAMENTO', $this->design['border'], 1, 'L', true);
        $this->Ln(2);
        
        $this->SetFont($this->design['font'], '', 10);
        $this->Cell(40, 7, 'Equipamento:', 0, 0);
        $this->Cell(0, 7, $data['equipamento'], 0, 1);
        $this->Ln(3);
        
        // Seção 4 - Defeito e Laudo
        $this->SetFont($this->design['font'], 'B', 12);
        $this->Cell(0, 8, 'DEFEITO RELATADO', $this->design['border'], 1, 'L', true);
        $this->Ln(2);
        
        $this->SetFont($this->design['font'], '', 10);
        $this->MultiCell(0, 7, $data['defeito'], 0, 1);
        $this->Ln(3);
        
        $this->SetFont($this->design['font'], 'B', 12);
        $this->Cell(0, 8, 'LAUDO TÉCNICO', $this->design['border'], 1, 'L', true);
        $this->Ln(2);
        
        $this->SetFont($this->design['font'], '', 10);
        $this->MultiCell(0, 7, $data['laudo'], 0, 1);
        $this->Ln(5);
        
        // Seção 5 - Serviço e Valor
        $this->SetFont($this->design['font'], 'B', 12);
        $this->Cell(0, 8, 'SERVIÇO PRESTADO', $this->design['border'], 1, 'L', true);
        $this->Ln(2);
        
        $this->SetFont($this->design['font'], '', 10);
        $this->MultiCell(0, 7, $data['servico'], 0, 1);
        $this->Ln(3);
        
        $this->Cell(40, 7, 'Valor:', 0, 0);
        $this->Cell(0, 7, 'R$ ' . number_format($data['valor'], 2, ',', '.'), 0, 1);
        $this->Ln(3);
        
        $this->Cell(40, 7, 'Prazo:', 0, 0);
        $this->Cell(0, 7, $data['prazo'], 0, 1);
        
        $this->Cell(40, 7, 'Garantia:', 0, 0);
        $this->Cell(0, 7, $data['garantia'], 0, 1);
        $this->Ln(10);
        
        // Seção 6 - Responsável e Assinatura
        $this->SetFont($this->design['font'], 'B', 12);
        $this->Cell(0, 8, 'RESPONSÁVEL TÉCNICO', $this->design['border'], 1, 'L', true);
        $this->Ln(2);
        
        $this->SetFont($this->design['font'], '', 10);
        $this->Cell(40, 7, 'Técnico:', 0, 0);
        $this->Cell(0, 7, $data['tecnico'], 0, 1);
        $this->Ln(15);
        
        // Assinatura
        $this->SetFont($this->design['font'], '', 10);
        $this->Cell(0, 7, 'Assinatura do Cliente', 0, 1, 'C');
        $this->Cell(0, 1, '', 'T', 1);
        
        // Geração do arquivo
        $filename = $data['numero_os'] . '_' . preg_replace('/[^a-z0-9]/i', '_', $data['cliente']);
        $pdfPath = $this->imagesDir . '/' . $filename . '.pdf';
        $base64Path = $this->outputDir . '/' . $filename . '.txt';
        
        $this->Output('F', $pdfPath);
        $base64Content = base64_encode(file_get_contents($pdfPath));
        file_put_contents($base64Path, $base64Content);
        
        return [
            'pdf_path' => $pdfPath,
            'base64_path' => $base64Path,
            'design' => $this->design
        ];
    }
}
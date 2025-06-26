<?php
namespace App\PDF;

require_once __DIR__ . '/../../vendor/autoload.php';

use FPDF;
use App\Models\Os;
use App\Models\Client;

class PDFGenerator extends FPDF {
    private $outputDir;
    private $base64Dir;
    private $imagesDir;

    public function __construct() {
        parent::__construct('P', 'mm', 'A4');
        $this->outputDir = __DIR__ . '/../../base64';
        $this->base64Dir = __DIR__ . '/../../base64';
        $this->imagesDir = __DIR__ . '/../../public/assets/pdf';

        // Create directories if they don't exist
        if (!file_exists($this->outputDir)) {
            mkdir($this->outputDir, 0777, true);
        }
        if (!file_exists($this->imagesDir)) {
            mkdir($this->imagesDir, 0777, true);
        }
    }

    public function Header() {
        // Dynamic design: Random header color
        $colors = [
            [33, 37, 41],   // Dark Gray
            [0, 123, 255],  // Blue
            [40, 167, 69],  // Green
            [220, 53, 69]   // Red
        ];
        $color = $colors[array_rand($colors)];
        $this->SetTextColor($color[0], $color[1], $color[2]);

        // Dynamic style: Alternate header style
        $style = rand(0, 1);
        $this->SetFont('Arial', 'B', 16);
        if ($style === 0) {
            $this->Cell(0, 10, 'ORDEM DE SERVIÇO', 0, 1, 'C');
            $this->Ln(5);
        } else {
            $this->SetFillColor($color[0], $color[1], $color[2]);
            $this->SetTextColor(255, 255, 255);
            $this->Cell(0, 12, 'ORDEM DE SERVIÇO', 0, 1, 'C', true);
            $this->Ln(5);
            $this->SetTextColor(0, 0, 0);
        }
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(128, 128, 128);
        $this->Cell(0, 10, 'Página ' . $this->PageNo(), 0, 0, 'C');
    }

    public function generateOS($orderId) {
        // Fetch order and client data
        $order = Os::with('client')->find($orderId);
        if (!$order) {
            throw new \Exception("Order not found");
        }
        $client = $order->client;

        $this->AddPage();

        // Random section background colors
        $sectionColors = [
            [240, 240, 240], // Light Gray
            [200, 220, 255], // Light Blue
            [200, 255, 200], // Light Green
        ];
        $sectionColor = $sectionColors[array_rand($sectionColors)];

        // Client Information
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor($sectionColor[0], $sectionColor[1], $sectionColor[2]);
        $this->Cell(0, 8, 'DADOS DO CLIENTE', 0, 1, 'L', true);
        $this->Ln(2);
        $this->SetFont('Arial', '', 11);

        $this->Cell(50, 8, 'ID da OS:', 0, 0);
        $this->Cell(0, 8, $order->id, 0, 1);
        $this->Cell(50, 8, 'Cliente:', 0, 0);
        $this->SetFont('Arial', 'B', 11);
        $this->Cell(0, 8, $client->name, 0, 1);
        $this->SetFont('Arial', '', 11);
        $this->Cell(50, 8, 'Veículo:', 0, 0);
        $this->Cell(0, 8, $client->vehicle, 0, 1);
        $this->Cell(50, 8, 'Placa:', 0, 0);
        $this->Cell(0, 8, $client->license_plate, 0, 1);
        $this->Cell(50, 8, 'Telefone:', 0, 0);
        $this->Cell(0, 8, $client->phone, 0, 1);
        $this->Cell(50, 8, 'Data:', 0, 0);
        $this->Cell(0, 8, date('d/m/Y', strtotime($order->created_at)), 0, 1);
        $this->Ln(5);

        // Service Details
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor($sectionColor[0], $sectionColor[1], $sectionColor[2]);
        $this->Cell(0, 8, 'DETALHES DO SERVIÇO', 0, 1, 'L', true);
        $this->Ln(2);
        $this->SetFont('Arial', '', 11);

        $this->Cell(50, 8, 'Descrição:', 0, 0);
        $this->MultiCell(0, 8, $order->description, 0, 1);
        $this->Cell(50, 8, 'Peças Usadas:', 0, 0);
        $this->MultiCell(0, 8, $order->parts_used ?? 'Nenhuma', 0, 1);
        $this->Cell(50, 8, 'Valor:', 0, 0);
        $this->Cell(0, 8, 'R$ ' . number_format($order->total_value, 2, ',', '.'), 0, 1);
        $this->Cell(50, 8, 'Status:', 0, 0);
        $this->Cell(0, 8, ucfirst(str_replace('_', ' ', $order->status)), 0, 1);
        $this->Ln(5);

        // Additional Notes (Optional)
        $this->SetFont('Arial', 'B', 12);
        $this->SetFillColor($sectionColor[0], $sectionColor[1], $sectionColor[2]);
        $this->Cell(0, 8, 'OBSERVAÇÕES', 0, 1, 'L', true);
        $this->Ln(2);
        $this->SetFont('Arial', '', 11);
        $this->MultiCell(0, 8, 'Nenhuma observação adicional.', 0, 1);
        $this->Ln(5);

        // Technical Responsible
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 8, 'RESPONSÁVEL TÉCNICO', 0, 1, 'L', true);
        $this->Ln(2);
        $this->SetFont('Arial', '', 11);
        $this->Cell(50, 8, 'Técnico:', 0, 0);
        $this->Cell(0, 8, 'Técnico Padrão', 0, 1); // Placeholder: Update with real technician data
        $this->Ln(15);

        // Signature
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 8, 'Assinatura do Cliente ______________________________', 0, 1, 'C');
        $this->Cell(0, 8, 'Assinatura do Técnico ______________________________', 0, 1, 'C');

        // Generate unique filename
        $filename = 'OS_' . $order->id . '_' . date('YmdHis');
        $pdfPath = $this->imagesDir . '/' . $filename . '.pdf';
        $base64Path = $this->base64Dir . '/' . $filename . '.txt';

        // Save PDF
        $this->Output('F', $pdfPath);

        // Generate and save Base64
        $pdfContent = file_get_contents($pdfPath);
        $base64Content = base64_encode($pdfContent);
        file_put_contents($base64Path, $base64Content);

        return [
            'pdf_path' => $pdfPath,
            'base64_path' => $base64Path,
            'filename' => $filename . '.pdf'
        ];
    }
}
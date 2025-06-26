<?php
require_once __DIR__ . '/vendor/autoload.php';

use PDFGeneratorOS\PDF_generator;

function clearTerminal() {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        system('cls');
    } else {
        system('clear');
    }
}

function showMenu() {
    clearTerminal();
    echo "=================================\n";
    echo "    GERADOR DE ORDEM DE SERVIÇO   \n";
    echo "=================================\n\n";
    
    echo "Digite os dados da OS:\n";
    $data = [
        'cliente' => readline("Nome do Cliente: "),
        'equipamento' => readline("Equipamento: "),
        'defeito' => readline("Defeito relatado: "),
        'servico' => readline("Serviço a ser realizado: "),
        'laudo' => readline("Laudo técnico: "),
        'valor' => (float) str_replace(',', '.', readline("Valor do Serviço: R$ ")),
        'tecnico' => readline("Nome do Técnico: "),
        'prazo' => readline("Prazo de entrega: "),
        'garantia' => readline("Garantia: "),
        'data' => date('d/m/Y')
    ];
    
    return $data;
}

function processPDF($data) {
    echo "\nAguarde, processando PDF...\n";
    
    $pdfGenerator = new PDF_generator();
    $result = $pdfGenerator->generateOS($data);
    
    echo "\nPDF gerado com sucesso! (Design: " . json_encode($result['design']) . ")\n";
    return $result;
}

function afterGenerationMenu($pdfPath, $base64Path) {
    while (true) {
        echo "\nO que deseja fazer?\n";
        echo "1. Ver PDF\n";
        echo "2. Ver Base64\n";
        echo "3. Criar outra OS\n";
        echo "4. Sair\n";
        
        $option = readline("Opção: ");
        
        switch ($option) {
            case '1':
                if (file_exists($pdfPath)) {
                    shell_exec($pdfPath);
                } else {
                    echo "Arquivo PDF não encontrado!\n";
                }
                break;
            case '2':
                if (file_exists($base64Path)) {
                    $base64 = file_get_contents($base64Path);
                    echo "\nConteúdo Base64:\n";
                    echo substr($base64, 0, 100) . "... [truncado]\n";
                } else {
                    echo "Arquivo Base64 não encontrado!\n";
                }
                break;
            case '3':
                return true; // Voltar ao início
            case '4':
                exit(0);
            default:
                echo "Opção inválida!\n";
        }
    }
}

// Loop principal do programa
while (true) {
    $osData = showMenu();
    $result = processPDF($osData);
    
    $continue = afterGenerationMenu($result['pdf_path'], $result['base64_path']);
    if (!$continue) break;
}
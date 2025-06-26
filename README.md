# Gerador de Ordens de Serviço (OS) em PDF

Um gerador de PDF para ordens de serviço com:
- Design aleatório a cada geração
- Armazenamento em PDF e Base64
- Interface de terminal simples

## Funcionalidades

✔ Gera PDFs profissionais com layout aleatório  
✔ Armazena em formato PDF e Base64  
✔ Interface intuitiva via terminal  
✔ Número de OS automático  
✔ Multiplos campos de informação  

## Como usar

1. Instale as dependências:
```bash
composer install
```

2. Execute o programa:
```bash
php index.php
```

2. Execute o programa:
```bash
php index.php
```

3. Siga as instruções para preencher os dados da OS

Após gerar, você pode:

<li>Visualizar o PDF</li>

<li>Ver o conteúdo em Base64</li>

<li>Criar uma nova OS</li>


## Melhorias implementadas:

1. **Designs aleatórios**:
   - 4 estilos diferentes de cores e fontes
   - Cada geração tem um visual único

2. **Mais campos**:
   - Número da OS automático
   - Equipamento
   - Defeito relatado
   - Laudo técnico
   - Prazo de entrega
   - Garantia

3. **Layout melhorado**:
   - Seções bem definidas
   - Espaçamento adequado
   - Hierarquia visual clara

4. **Melhor tratamento de caracteres**:
   - Uso de iconv para caracteres especiais

5. **Sistema de numeração automática**:
   - Formato: OS-YYYYMMDDXXX

6. **Feedback do design usado**:
   - Mostra qual design foi aplicado no terminal

Para usar, basta executar `php index.php` e preencher todos os campos. Cada OS terá um visual único!

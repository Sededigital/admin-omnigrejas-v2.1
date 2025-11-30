<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Member</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Estilos para tela */
        @media screen {
            html, body {
                margin: 0;
                padding: 0;
                width: 100%;
                height: 100%;
                background: #f5f5f5;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .card-container {
                display: flex;
                justify-content: center;
                align-items: center;
                width: 100%;
                height: 100vh;
                background: white;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                border-radius: 8px;
                margin: 20px;
            }

        }

        /* Estilos para impressão - uma única página */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
                orphans: 0;
                widows: 0;
            }

            /* Forçar impressão em uma única página */
            html, body {
                height: 100vh !important;
                overflow: hidden !important;
            }

            html, body {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                height: 100% !important;
                position: relative !important;
                background: transparent !important;
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .card-container {
                position: absolute !important;
                top: 40% !important;
                left: 50% !important;
                transform: translate(-50%, -50%) !important;
                width: 100% !important;
                height: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                background: transparent !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }



            /* Garantir que não haja quebras de página em nenhum elemento */
            *, *::before, *::after {
                page-break-inside: avoid !important;
                page-break-before: avoid !important;
                page-break-after: avoid !important;
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        {!! $svgContent !!}
    </div>

    <script>
        // Abrir janela de impressão automaticamente
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };

        // Fallback para navegadores que não suportam window.onload
        if (document.readyState === 'complete') {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>


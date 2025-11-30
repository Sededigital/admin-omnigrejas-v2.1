<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificado de Reconhecimento Eclesiástico</title>
    <style>
        /* 1. Configurações Globais e da Página de Impressão (A4) */
        @page {
            size: A4;
            margin: 0; /* Certificados geralmente usam margem zero para o layout preencher a folha */
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #111;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Para centralizar na tela */
        }

        /* 2. Container Principal (A4 em Portrait) */
        .certificate-container {
            width: 21cm; /* Largura A4 */
            height: 29.7cm; /* Altura A4 */
            box-sizing: border-box; 
            margin: 0;
            padding: 2.5cm 3cm; 
            background: #ffffff;
            border: 1px solid #ddd;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
        }

        /* Borda Decorativa (Opcional, mas dá um toque clássico) */
        .certificate-container::before {
            content: '';
            position: absolute;
            top: 0.5cm;
            left: 0.5cm;
            right: 0.5cm;
            bottom: 0.5cm;
            border: 5px double #00008B; /* Azul Marinho Profissional */
            pointer-events: none;
        }
        
        /* 2.5. Número de Registro Superior */
        .registration-number {
            position: absolute;
            top: 1cm; /* Elevado para ficar acima do logo */
            right: 1cm; 
            font-size: 9pt;
            color: #555;
            font-weight: bold;
        }


        /* 3. Estilos do Título e Corpo */
        .header-logo {
            font-size: 14pt;
            font-weight: bold;
            color: #00008B;
            margin-bottom: 0px;
        }
        .main-title {
            font-size: 36pt;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            letter-spacing: 2px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-top: -740px;
        }
        .sub-title {
            font-size: 18pt;
            color: #333;
            margin-bottom: 40px;
            font-style: italic;
        }
        .body-text {
            font-size: 14pt;
            margin: 0 0 60px 0; 
            max-width: 100%; 
            text-align: left; /* Bloco agora encostado na esquerda, com texto à esquerda */
        }
        .member-name {
            font-size: 18pt; 
            font-family: 'Georgia', serif; 
            font-weight: bold;
            color: #00008B;
            display: block;
            margin: 15px 0;
            text-transform: uppercase;
            text-align: center; /* Mantém o nome centralizado para destaque */
            white-space: nowrap; 
        }
        .date-issued {
            font-size: 12pt;
            margin-top: 200px;
            margin-bottom: 60px;
            text-align: center; /* Mantém a data centralizada */
        }

        /* 4. Assinaturas */
        .signatures {
         
            width: 100%; /* Ajustado para 100% para ocupar o máximo de espaço horizontal */
            display: flex;
            justify-content: flex-start; 
            margin-top: auto; 
            padding: 0 5%; /* Adiciona um pequeno padding para que as linhas não encostem 100% nas margens do content-container */
            box-sizing: border-box;
        }
        .signature-line {
            width: 40%; /* Reduzido para dar mais margem de manobra */
            border-top: 1px solid #000;
            padding-top: 5px;
            font-size: 10pt;
            font-weight: bold;
            text-align: center; 
        }
        /* Garantir que a segunda assinatura vá para a direita */
        .signature-line.right-aligned {
            margin-left: auto; /* Força o alinhamento total à direita */
        }
        
        /* 5. Otimizações para Impressão */
        @media print {
            body {
                background-color: #ffffff;
                min-height: auto;
                justify-content: flex-start;
                align-items: flex-start;
            }
            .certificate-container {
                border: none;
                box-shadow: none;
                margin: 0;
                padding: 2.5cm 3cm; 
            }
            .header-logo, .member-name {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <div class="certificate-container">
        
        <!-- NOVO: Registro Interno no Canto Superior Direito (Mais alto) -->
        <div class="registration-number">
            Nº: 2025/OMNIGREJA-001
        </div>

        <!-- Topo: Identificação do Sistema -->
        <div class="header-logo">
            Omnigrejas Sistema de Gestão Eclesiástica
        </div>
        
        <!-- Título Principal -->
        <div class="main-content">
            <h1 class="main-title">CERTIFICADO DE RECONHECIMENTO</h1>
            <h3 class="sub-title">MÉRITO E SERVIÇO ECLESIÁSTICO</h3>
            
            <!-- Corpo do Certificado (Bloco Centralizado, Texto Alinhado à Esquerda) -->
            <p class="body-text">
                Certificamos que o(a) membro
                <span class="member-name">
                    NOME COMPLETO DO MEMBRO
                </span>
                foi reconhecido(a) com este título 
                <span style="font-style: italic;">
                    pela dedicação exemplar e contribuição inestimável ao corpo ministerial e administrativo da Igreja.
                </span>
                Este certificado atesta a excelência e o compromisso demonstrado.
            </p>

            <!-- Data de Emissão -->
            <div class="date-issued">
                Emitido em Cidade, em 
                <strong>
                    15 de Outubro de 2025
                </strong>.
            </div>
            
            <!-- Assinaturas -->
            <div class="signatures">
                <div class="signature-line">
                    Autoridade Eclesiástica / Pastor Sênior
                </div>
                <!-- Coordenador com margin-left: auto para ir para a direita -->
                <div class="signature-line right-aligned">
                    Coordenador do Sistema / Líder Administrativo
                </div>
            </div>
        </div>

    </div>
</body>
</html>

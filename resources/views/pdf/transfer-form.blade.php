<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Transferência Eclesiástica - {{ $member->user->name }}</title>
    <style>
        /* 1. Reset e Configurações Básicas */
        body {
            font-family: 'Times New Roman', serif; /* Fonte mais clássica/oficial */
            font-size: 11pt;
            line-height: 1.5;
            color: #111;
            margin: 0;
            padding: 15px;
            position: relative; /* Necessário para posicionamento absoluto do número do documento */
        }

        /* 2. Layouts Estruturais para PDF (Usando Table/Flow para Robustez) */
        .header-content, .signatures {
            width: 100%;
            display: table;
            table-layout: fixed;
            border-collapse: collapse;
        }
        /* Ajuste para centralizar o cabeçalho principal */
        .header-main-center {
            display: flex;
            flex-direction: column; /* Organiza logo acima do texto */
            align-items: center; /* Centraliza horizontalmente */
            text-align: center;
        }

        .header-col, .signature-box {
            display: table-cell;
            vertical-align: top;
        }
        .data-section {
            margin-bottom: 20px; /* Reduzido para compactação */
            padding: 8px; /* Reduzido para compactação */
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .two-columns {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
        .col {
            display: table-cell;
            width: 50%;
            padding: 0 10px;
            vertical-align: top;
        }

        /* 3. Estilização do Cabeçalho e Título */
        .header {
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 15px; /* Reduzido para compactação */
            position: relative; /* Para que o elemento absoluto seja posicionado em relação ao cabeçalho */
        }
        
        /* Contêiner para o Número do Documento no canto extremo superior direito */
        .doc-number-container {
            position: absolute;
            top: -10px; /* Ajuste para colocar bem no topo da margem (10mm = 1cm) */
            right: 0;
            width: 150px; /* Largura fixa para garantir o alinhamento */
            text-align: right;
            line-height: 1;
        }

        .church-info h1 {
            color: #111;
            font-size: 16pt;
            margin: 5pt 0 3pt 0; /* Espaço após o logo */
            font-weight: bold;
        }
        .church-info p {
            margin: 0;
            color: #333;
            font-size: 10pt;
        }
        .document-title {
            font-size: 14pt;
            font-weight: bold;
            color: #000;
            margin: 15px 0 8px 0; /* Reduzido para compactação */
            text-align: center;
            text-transform: uppercase;
        }
        .document-subtitle {
            text-align: center;
            margin-bottom: 10px; /* Reduzido para compactação */
            font-weight: normal;
            font-size: 11pt;
            font-style: italic;
            color: #444;
        }

        /* 4. Estilização dos Campos de Dados */
        .section-title {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 8px; /* Reduzido para compactação */
            color: #000;
            border-bottom: 1px dashed #666;
            padding-bottom: 3px; /* Reduzido para compactação */
            text-transform: uppercase;
        }
        .data-field {
            margin-bottom: 5px; /* Reduzido para compactação */
            display: block;
        }
        .data-label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
            color: #333;
        }
        .data-value {
            border-bottom: 1px dotted #888;
            padding-bottom: 1px;
            display: inline-block;
            min-width: 150px;
        }

        /* 5. Nota Importante */
        .important-note {
            background-color: #f2f2f2;
            border: 1px solid #aaa;
            border-radius: 4px;
            padding: 10px;
            margin: 25px 0 15px 0; /* Espaçamento mantido (25px) */
            font-size: 10pt;
            line-height: 1.4; /* Levemente reduzido */
            color: #333;
        }

        /* 6. Assinaturas */
        .signature-section {
            margin-top: 100px; /* AUMENTADO para 120px para empurrar mais para baixo */
            padding-top: 5px; /* Reduzido para compactação */
        }
        .signatures {
            width: 100%;
            display: table;
            table-layout: fixed;
            border-collapse: collapse;
        }
        .signature-box {
            text-align: center;
            width: 33.3%;
            padding: 0 5px; /* Reduzido para compactação */
            display: table-cell;
            vertical-align: top;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            width: 90%;
            margin: 30px auto 3px auto; /* Reduzido espaço acima e abaixo da linha */
            display: block;
        }
        .signature-label {
            font-size: 10pt;
            color: #111;
            margin-top: 3px; /* Reduzido para compactação */
            line-height: 1.2; /* Reduzido para compactação */
        }
        .signature-label strong {
            font-size: 11pt;
        }

        /* 7. Rodapé */
        .footer {
            text-align: center;
            font-size: 8pt;
            color: #555;
            padding-top: 5px; /* Reduzido para compactação */
            margin-top: 15px; /* Reduzido para compactação */
            border-top: 1px solid #eee;
        }

        /* Estilo para garantir que o logo se ajuste ou mostre um placeholder (CIRCULAR E SEM BORDA) */
        .church-logo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: none; /* Sem borda, conforme solicitado */
            border-radius: 50%; /* Circular, conforme solicitado */
            margin: 0 auto; /* Centraliza o logo dentro do contêiner */
            display: block;
        }
        .logo-placeholder {
            width: 80px;
            height: 80px;
            border: none; /* Sem borda, conforme solicitado */
            border-radius: 50%; /* Circular, conforme solicitado */
            background-color: #f2f2f2; /* Fundo leve para o placeholder */
            text-align: center;
            line-height: 80px;
            font-size: 10pt;
            font-weight: bold;
            margin: 0 auto; /* Centraliza o placeholder */
            display: block;
        }
        
        /* 8. Configuração de Impressão (A4) - Margem Mínima de 10mm */
        @page {
            size: A4;
            margin: 10mm; /* Margem de 1cm em todos os lados para respiro */
        }

        .styled-hr {
            border: none;
            height: 1px;
            background-color: #d1d5db; /* Cinza claro */
            margin: 1rem 0;
        }
        .system-logo {
            position: absolute;
            top: -70px;
            left: -30px;
            width: 140px;
            height: 140px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    @php
    // Para garantir a formatação em Português (BR) dependendo do pacote de idioma
    // do sistema (Carbon), usamos o isoFormat().
    // O 'LL' retorna a data por extenso (Ex: 15 de Outubro de 2025).
        $data_pt_br = $migrationDate->locale('pt-BR')->isoFormat('LL');
    @endphp

    <!-- Logo do Sistema - Canto Superior Esquerdo -->
    <img src="{{ asset('system/img/logo-system/icon.png') }}" alt="Omnigrejas" class="system-logo">


    <!-- Contêiner para o Número do Documento no Canto Superior Direito Absoluto -->
    <div class="doc-number-container">
        <p style="font-size: 9pt; color: #777; margin: 0;">
            DOC. N.º: {{ $migrationDate->format('Y/m/d') }}/{{ $member->numero_membro }}-{{ $migrationDate->timestamp }}
        </p>
    </div>

    <!-- Cabeçalho com Logo e Informações da Igreja - Centralizados -->
    <div class="header">
        <div class="header-main-center">
            @if($sourceChurch->logo)
                <img src="{{ Storage::disk('supabase')->url($sourceChurch->logo) }}" alt="Logo da Igreja" class="church-logo">
            @else
                <div class="logo-placeholder">LOGO</div>
            @endif
            
            <div class="church-info">
                <h1>{{ $sourceChurch->nome }}</h1>
                <p>{{ $sourceChurch->sigla ? '(' . $sourceChurch->sigla . ')' : '' }}</p>
                <!-- Alterado de Localização para NIF -->
                <p>NIF: {{ $sourceChurch->nif ?? 'Não informado' }}</p>
                <p>Contato: {{ $sourceChurch->contacto ?? 'Não informado' }}</p>
            </div>
        </div>
    </div>

    <!-- Título do Documento -->
    <div class="document-title">
        @php
            $documentType = match($migrationType) {
                'reintegracao' => 'DECLARAÇÃO E FICHA DE REINTEGRAÇÃO ECLESIÁSTICA',
                'mudanca_cargo' => 'DECLARAÇÃO E FICHA DE MUDANÇA DE CARGO ECLESIÁSTICO',
                'nova_adesao' => 'DECLARAÇÃO E FICHA DE NOVA ADESÃO ECLESIÁSTICA',
                default => 'DECLARAÇÃO E FICHA DE TRANSFERÊNCIA ECLESIÁSTICA'
            };
        @endphp
        {{ $documentType }}
    </div>

    <!-- Tipo de Transferência -->
    <div class="document-subtitle">
        Transferência: {{ $targetChurch ? 'PARA IGREJA CADASTRADA (' . $targetChurch->nome . ')' : 'PARA IGREJA EXTERNA (' . $targetChurchName . ')' }}
    </div>

    <!-- Seção de Informações do Membro (2 Colunas) -->
    <div class="data-section">
        <div class="section-title">
            INFORMAÇÕES DO MEMBRO
        </div>

        <div class="two-columns">
            <!-- Coluna 1 -->
            <div class="col">
                <div class="data-field">
                    <span class="data-label">Nome Completo:</span>
                    <span class="data-value">{{ $member->user->name }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Data de Entrada:</span>
                    <span class="data-value">{{ $member->data_entrada ? $member->data_entrada->format('d/m/Y') : 'Não informada' }}</span>
                </div>
            </div>

            <!-- Coluna 2 -->
            <div class="col">
                <div class="data-field">
                    <span class="data-label">Nº de Membro:</span>
                    <span class="data-value">{{ $member->numero_membro }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Cargo Atual:</span>
                    <span class="data-value">{{ ucfirst($member->cargo) }}</span>
                </div>
            </div>
        </div>
        <div class="data-field" style="padding-top: 5px;">
            <span class="data-label">Email de Contato:</span>
            <span class="data-value">{{ $member->user->email }}</span>
        </div>
    </div>

    <!-- Seção de Detalhes da Transferência (2 Colunas) -->
    <div class="data-section">
        <div class="section-title">
            DETALHES DA TRANSFERÊNCIA
        </div>

        <div class="two-columns">
            <!-- Coluna 1 -->
            <div class="col">
                <div class="data-field">
                    <span class="data-label">Igreja de Origem:</span>
                    <span class="data-value">{{ $sourceChurch->nome }} {{ $sourceChurch->sigla ? '(' . $sourceChurch->sigla . ')' : '' }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Novo Cargo:</span>
                    <span class="data-value">{{ $newRole ? ucfirst($newRole) : 'Manter cargo atual (' . ucfirst($member->cargo) . ')' }}</span>
                </div>
            </div>

            <!-- Coluna 2 -->
            <div class="col">
                <div class="data-field">
                    <span class="data-label">Igreja de Destino:</span>
                    <span class="data-value">
                        @if($targetChurch)
                            {{ $targetChurch->nome }} {{ $targetChurch->sigla ? '(' . $targetChurch->sigla . ')' : '' }}
                        @else
                            {{ $targetChurchName }} (Externa)
                        @endif
                    </span>
                </div>
                <div class="data-field">
                    <span class="data-label">Data da Migração:</span>
                    <span class="data-value">{{ $migrationDate->format('d/m/Y \à\s H:i') }}</span>
                </div>
            </div>
        </div>
        <div class="data-field" style="padding-top: 5px;">
            <span class="data-label">Motivo da Transferência:</span>
            <span class="data-value">{{ $reason ?: 'Não informado' }}</span>
        </div>
    </div>

    <!-- Nota Importante/Declaração -->
    <div class="important-note">
        <strong>DECLARAÇÃO OFICIAL:</strong>
        A Igreja de Origem, <span style="font-weight: bold;">{{ $sourceChurch->nome }}</span>,
        atesta a idoneidade, bom testemunho e a plena comunhão do membro
        <span style="font-weight: bold;">{{ $member->user->name }}</span>,
        e o transfere oficialmente, a partir de <span style="font-weight: bold;">{{ $data_pt_br }}</span>,
        para a
        @if($targetChurch)
            <span style="font-weight: bold;">{{ $targetChurch->nome }}</span>.
        @else
            igreja externa <span style="font-weight: bold;">{{ $targetChurchName }}</span>.
        @endif
        O membro está liberado para exercer suas atividades eclesiásticas na nova congregação.
        Esta declaração tem validade jurídica para fins eclesiásticos.
    </div>
    
    <hr class="styled-hr">

    <!-- Seção de Assinaturas (3 Colunas) -->
    <div class="signature-section">
        <div class="signatures">
            <!-- Coluna 1: Responsável pela Migração -->
            <div class="signature-box">
                <span class="signature-line"></span>
                <div class="signature-label">
                    <strong style="text-transform: uppercase;">{{ $migratedBy->name }}</strong><br>
                    {{ ucfirst($migratedBy->role) }}<br>
                    Responsável pela Migração (Interno)
                </div>
            </div>

            <!-- Coluna 2: Igreja de Origem -->
            <div class="signature-box">
                <span class="signature-line"></span>
                <div class="signature-label">
                    <strong style="text-transform: uppercase;">{{ $sourceChurch->nome }}</strong><br>
                    Líder Eclesiástico de Origem<br>
                    Carimbo e Assinatura
                </div>
            </div>

            <!-- Coluna 3: Membro Transferido -->
            <div class="signature-box">
                <span class="signature-line"></span>
                <div class="signature-label">
                    <strong style="text-transform: uppercase;">{{ $member->user->name }}</strong><br>
                    Membro Transferido<br>
                    Assinatura de Confirmação
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <div class="footer">
        <p>Documento gerado pelo **Sistema Omnigreja** em {{ now()->format('d/m/Y H:i:s') }} (ID de Rastreio: {{ $migrationDate->timestamp }})</p>
        <p>A autenticidade deste documento pode ser verificada junto à igreja de origem.</p>
    </div>
</body>
</html>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Membro - {{ $member->user->name }}</title>
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

        /* Foto do Membro */
        .member-photo {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #dae0e6;
            margin: 0 auto;
            display: block;
        }
        .photo-placeholder {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: #e9ecef;
            border: 2px solid #ced5de;
            text-align: center;
            line-height: 80px;
            font-size: 24px;
            font-weight: bold;
            color: #6c757d;
            margin: 0 auto;
            display: block;
        }

        /* Código QR */
        .qr-code {
            width: 60px;
            height: 60px;
            margin: 0 auto;
            display: block;
        }
    </style>
</head>
<body>
    @php
    // Para garantir a formatação em Português (BR) dependendo do pacote de idioma
    // do sistema (Carbon), usamos o 'LL' retorna a data por extenso (Ex: 15 de Outubro de 2025).
        $data_emissao = now()->locale('pt-BR')->isoFormat('LL');
        $numero_membro = $member->numero_membro ?? 'N/A';
        $data_entrada = $member->data_entrada ? $member->data_entrada->locale('pt-BR')->isoFormat('LL') : 'Não informada';
    @endphp

    <!-- Logo do Sistema - Canto Superior Esquerdo -->
    <img src="{{ asset('system/img/logo-system/icon.png') }}" alt="Omnigrejas" class="system-logo">

    <!-- Contêiner para o Número do Documento no Canto Superior Direito Absoluto -->
    <div class="doc-number-container">
        <p style="font-size: 9pt; color: #777; margin: 0;">
            DOC. N.º: {{ now()->format('Y/m/d') }}/{{ $numero_membro }}-{{ now()->timestamp }}
        </p>
    </div>

    <!-- Cabeçalho com Logo e Informações da Igreja - Centralizados -->
    <div class="header">
        <div class="header-main-center">
            @if($member->igreja->logo)
                <img src="{{ Storage::disk('supabase')->url($member->igreja->logo) }}" alt="Logo da Igreja" class="church-logo">
            @else
                <div class="logo-placeholder">LOGO</div>
            @endif

            <div class="church-info">
                <h1>{{ $member->igreja->nome }}</h1>
                <p>{{ $member->igreja->sigla ? '(' . $member->igreja->sigla . ')' : '' }}</p>
                <p>NIF: {{ $member->igreja->nif ?? 'Não informado' }}</p>
                <p>Contato: {{ $member->igreja->contacto ?? 'Não informado' }}</p>
            </div>
        </div>
    </div>

    <!-- Título do Documento -->
    <div class="document-title">
        FICHA DE MEMBRO
    </div>

    <!-- Subtítulo -->
    <div class="document-subtitle">
        Documento oficial de identificação eclesiástica
    </div>

    <!-- Seção de Informações do Membro (2 Colunas) -->
    <div class="data-section">
        <div class="section-title">
            INFORMAÇÕES PESSOAIS
        </div>

        <!-- Foto do Membro -->
        <div style="text-align: center; margin-bottom: 15px;">
            @if($member->user->photo_url)
                <img src="{{ Storage::disk('supabase')->url($member->user->photo_url) }}" alt="Foto do Membro" class="member-photo">
            @else
                <div class="photo-placeholder">{{ strtoupper(substr($member->user->name ?? 'N', 0, 2)) }}</div>
            @endif
        </div>

        <div class="two-columns">
            <!-- Coluna 1 -->
            <div class="col">
                <div class="data-field">
                    <span class="data-label">Nome Completo:</span>
                    <span class="data-value">{{ $member->user->name }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Email:</span>
                    <span class="data-value">{{ $member->user->email }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Telefone:</span>
                    <span class="data-value">{{ $member->user->phone ?? 'Não informado' }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Gênero:</span>
                    <span class="data-value">{{ ucfirst($member->membroPerfil->genero ?? 'Não informado') }}</span>
                </div>
            </div>

            <!-- Coluna 2 -->
            <div class="col">
                <div class="data-field">
                    <span class="data-label">Nº de Membro:</span>
                    <span class="data-value">{{ $numero_membro }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Cargo:</span>
                    <span class="data-value">{{ ucfirst($member->cargo) }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Data de Entrada:</span>
                    <span class="data-value">{{ $data_entrada }}</span>
                </div>
                <div class="data-field">
                    <span class="data-label">Status:</span>
                    <span class="data-value">{{ ucfirst($member->status) }}</span>
                </div>
            </div>
        </div>

        <!-- Endereço em linha separada -->
        <div class="data-field" style="padding-top: 5px;">
            <span class="data-label">Endereço:</span>
            <span class="data-value">{{ $member->membroPerfil->endereco ?? 'Não informado' }}</span>
        </div>

        <!-- Observações se existirem -->
        @if($member->membroPerfil->observacoes)
        <div class="data-field" style="padding-top: 5px;">
            <span class="data-label">Observações:</span>
            <span class="data-value">{{ $member->membroPerfil->observacoes }}</span>
        </div>
        @endif
    </div>

    <!-- Seção de Ministérios (se houver) -->
    @if($member->ministerios && $member->ministerios->count() > 0)
    <div class="data-section">
        <div class="section-title">
            MINISTÉRIOS
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 10px;">
            @foreach($member->ministerios as $ministerio)
                <span style="background: #e9ecef; padding: 3px 8px; border-radius: 12px; font-size: 9pt; color: #495057;">
                    {{ $ministerio->nome }}
                </span>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Informações Institucionais -->
    <div class="important-note" style="background-color: #e3f2fd; border: 1px solid #2196f3; margin-bottom: 15px;">
        <strong style="color: #1976d2;">SISTEMA OMNIGREJAS - GESTÃO ECLESIÁSTICA</strong><br>
        <small style="color: #424242;">
            Documento oficial emitido pelo Sistema Omnigreja para controle e identificação eclesiástica.
            Este documento é confidencial e destina-se exclusivamente ao uso interno da instituição religiosa.
            Validade: Indeterminada, sujeito às normas eclesiásticas vigentes.
        </small>
    </div>

    <!-- Nota Importante/Declaração -->
    <div class="important-note">
        <strong>DECLARAÇÃO OFICIAL:</strong>
        A Igreja {{ $member->igreja->nome }} certifica que
        <span style="font-weight: bold;">{{ $member->user->name }}</span>
        é membro ativo desta congregação, registrado sob o número
        <span style="font-weight: bold;">{{ $numero_membro }}</span>,
        com direitos e deveres conforme o estatuto eclesiástico.
        Esta ficha é válida para identificação eclesiástica e deve ser apresentada quando solicitado.
    </div>

    <!-- Data de Emissão -->
    <div style="text-align: center; margin-top: 30px; padding: 10px; border: 1px solid #000; display: inline-block;">
        <strong>DATA DE EMISSÃO: {{ $data_emissao }}</strong>
    </div>

    <!-- Código QR (se disponível) -->
    @if(isset($qrCode) && $qrCode)
    <div style="text-align: center; margin-top: 20px;">
        <div style="display: inline-block; border: 1px solid #ccc; padding: 5px;">
            {!! $qrCode !!}
        </div>
        <p style="font-size: 8pt; color: #666; margin-top: 5px;">Código QR de Validação</p>
    </div>
    @endif

    <!-- Rodapé -->
    <div class="footer" style="margin-top: 80px;">
        <p>Documento gerado pelo **Sistema Omnigreja** em {{ now()->format('d/m/Y H:i:s') }} (ID de Rastreio: {{ now()->timestamp }})</p>
        <p>A autenticidade deste documento pode ser verificada junto à igreja emissora.</p>
    </div>
</body>
</html>

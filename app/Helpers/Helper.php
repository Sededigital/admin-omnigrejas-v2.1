<?php


if (! function_exists('minifyCss')) {
    function minifyCss(string $css): string {
        // 1. Remove comentários /* ... */
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

        // 2. Remove quebras, tabs e múltiplos espaços
        $css = str_replace(["\r\n","\r","\n","\t"], ' ', $css);

        // 3. Remove espaços antes/depois de chaves, dois-pontos, ponto-e-vírgula, vírgulas
        $css = preg_replace('/\s*([{};:,])\s*/', '$1', $css);

        // 4. Remove ponto-e-vírgula antes de }
        $css = str_replace(';}', '}', $css);

        return trim($css);
    }
}


if (! function_exists('minifyJs')) {
    function minifyJs(string $js): string {
        // Remove comentários de linha e bloco
        $js = preg_replace([
            '/\/\/[^\n\r]*/',       // // comentário de linha
            '/\/\*[\s\S]*?\*\//',   // /* comentário de bloco */
        ], '', $js);

        // Remove quebras e tabs
        $js = str_replace(["\r\n", "\r", "\n", "\t"], ' ', $js);

        // Remove espaços desnecessários ao redor de operadores e chaves
        $js = preg_replace('/\s*([{};.,=\+\-\*\/\(\)\[\]])\s*/', '$1', $js);

        return trim($js);
    }
}


if (!function_exists('abreviarNome')) {
    function abreviarNome($nomeCompleto)
    {
        $ignoradas = ['de', 'da', 'do', 'dos', 'das'];
        $partes = preg_split('/\s+/', trim($nomeCompleto));
        $quantidadePalavras = count($partes);
        $nomeSemEspacos = str_replace(' ', '', $nomeCompleto);
        $comprimentoSemEspaco = mb_strlen($nomeSemEspacos);

        if ($quantidadePalavras > 3) {
            $abreviado = [];
            $abreviado[] = $partes[0];
            for ($i = 1; $i < $quantidadePalavras - 1; $i++) {
                if (!in_array(mb_strtolower($partes[$i]), $ignoradas)) {
                    $abreviado[] = strtoupper(mb_substr($partes[$i], 0, 1)) . '.';
                }
            }
            $abreviado[] = $partes[$quantidadePalavras - 1];
            return implode(' ', $abreviado);
        }

        if ($quantidadePalavras <= 3 && $comprimentoSemEspaco > 20) {
            $abreviado = [];
            $abreviado[] = $partes[0];
            if ($quantidadePalavras === 3) {
                $meio = $partes[1];
                if (!in_array(mb_strtolower($meio), $ignoradas)) {
                    $abreviado[] = strtoupper(mb_substr($meio, 0, 1)) . '.';
                }
            }
            $abreviado[] = $partes[$quantidadePalavras - 1];
            return implode(' ', $abreviado);
        }

        return $nomeCompleto;
    }
}

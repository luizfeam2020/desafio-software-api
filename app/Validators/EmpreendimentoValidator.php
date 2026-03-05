<?php declare(strict_types=1);

namespace App\Validators;

final class EmpreendimentoValidator
{
    public function validarCriacao(array $in): array
    {
        $erros = [];

        $nome = trim((string)($in['nome'] ?? ''));
        $municipio = trim((string)($in['municipio'] ?? ''));
        $uf = strtoupper(trim((string)($in['uf'] ?? 'SC')));

        if ($nome === '') $erros['nome'] = 'Campo obrigatório.';
        if ($municipio === '') $erros['municipio'] = 'Campo obrigatório.';
        if ($uf !== 'SC') $erros['uf'] = 'A API aceita apenas empreendimentos em SC (uf deve ser "SC").';

        $this->validarOpcionais($in, $erros);

        return $erros;
    }

    public function validarAtualizacao(array $in): array
    {
        $erros = [];

        if (array_key_exists('uf', $in)) {
            $uf = strtoupper(trim((string)$in['uf']));
            if ($uf !== 'SC') $erros['uf'] = 'A API aceita apenas SC (uf deve ser "SC").';
        }

        if (array_key_exists('nome', $in) && trim((string)$in['nome']) === '') {
            $erros['nome'] = 'Se informado, não pode ser vazio.';
        }

        if (array_key_exists('municipio', $in) && trim((string)$in['municipio']) === '') {
            $erros['municipio'] = 'Se informado, não pode ser vazio.';
        }

        $this->validarOpcionais($in, $erros);

        return $erros;
    }

    private function validarOpcionais(array $in, array &$erros): void
    {
        if (array_key_exists('cnpj', $in) && $in['cnpj'] !== null && trim((string)$in['cnpj']) !== '') {
            $cnpj = preg_replace('/\D+/', '', (string)$in['cnpj']);
            if (strlen($cnpj) !== 14) $erros['cnpj'] = 'CNPJ deve ter 14 dígitos (somente números).';
        }

        if (array_key_exists('cep', $in) && $in['cep'] !== null && trim((string)$in['cep']) !== '') {
            $cep = preg_replace('/\D+/', '', (string)$in['cep']);
            if (strlen($cep) !== 8) $erros['cep'] = 'CEP deve ter 8 dígitos (somente números).';
        }

        if (array_key_exists('ativo', $in) && $in['ativo'] !== null && $in['ativo'] !== '') {
            $v = $in['ativo'];
            if (!in_array($v, [0, 1, '0', '1', true, false], true)) {
                $erros['ativo'] = 'Valor inválido. Use 0/1.';
            }
        }
    }
}
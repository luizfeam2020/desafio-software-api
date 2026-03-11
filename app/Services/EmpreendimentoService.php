<?php declare(strict_types=1);

namespace App\Services;

use App\Repositories\EmpreendimentoRepository;
use App\Validators\EmpreendimentoValidator;
use PDOException;

final class EmpreendimentoService
{
    public function __construct(
        private EmpreendimentoRepository $repo,
        private EmpreendimentoValidator $validator
    ) {}

    public function criar(array $payload): array
    {
        $erros = $this->validator->validarCriacao($payload);
        if ($erros) return ['ok' => false, 'status' => 422, 'erro' => 'Falha de validação', 'campos' => $erros];

        $agora = date('c');

        $dados = [
            'nome' => trim((string)$payload['nome']),
            'cnpj' => $this->normNull($payload['cnpj'] ?? null, fn($v) => preg_replace('/\D+/', '', (string)$v)),
            'municipio' => trim((string)$payload['municipio']),
            'uf' => 'SC',
            'endereco' => $this->normNull($payload['endereco'] ?? null, fn($v) => trim((string)$v)),
            'cep' => $this->normNull($payload['cep'] ?? null, fn($v) => preg_replace('/\D+/', '', (string)$v)),
            'segmento' => $this->normNull($payload['segmento'] ?? null, fn($v) => trim((string)$v)),
            'ativo' => array_key_exists('ativo', $payload) ? (int)((bool)$payload['ativo']) : 1,
            'criado_em' => $agora,
            'atualizado_em' => $agora,
        ];

        try {
            $id = $this->repo->criar($dados);
            return ['ok' => true, 'status' => 201, 'dados' => $this->repo->buscarPorId($id)];
        } catch (PDOException $e) {
            if (str_contains(strtolower($e->getMessage()), 'unique')) {
                return ['ok' => false, 'status' => 409, 'erro' => 'Conflito: registro duplicado (ex.: cnpj já cadastrado).'];
            }
            throw $e;
        }
    }

    public function buscar(int $id): array
    {
        $obj = $this->repo->buscarPorId($id);
        if (!$obj) return ['ok' => false, 'status' => 404, 'erro' => 'Empreendimento não encontrado.'];
        return ['ok' => true, 'status' => 200, 'dados' => $obj];
    }

    public function listar(array $query): array
    {
        $limit = isset($query['limit']) ? max(1, min(100, (int)$query['limit'])) : 20;
        $offset = isset($query['offset']) ? max(0, (int)$query['offset']) : 0;

        $filtros = [
            'nome' => isset($query['nome']) ? trim((string)$query['nome']) : null,
            'municipio' => isset($query['municipio']) ? trim((string)$query['municipio']) : null,
            'segmento' => isset($query['segmento']) ? trim((string)$query['segmento']) : null,
            'ativo' => $query['ativo'] ?? null,
        ];

        $res = $this->repo->listar($filtros, $limit, $offset);

        return [
            'ok' => true,
            'status' => 200,
            'dados' => [
                'total' => $res['total'],
                'limit' => $limit,
                'offset' => $offset,
                'itens' => $res['itens'],
                'filtros' => $filtros,
            ],
        ];
    }

    public function atualizar(int $id, array $payload): array
    {
        if (!$this->repo->buscarPorId($id)) return ['ok' => false, 'status' => 404, 'erro' => 'Empreendimento não encontrado.'];

        $erros = $this->validator->validarAtualizacao($payload);
        if ($erros) return ['ok' => false, 'status' => 422, 'erro' => 'Falha de validação', 'campos' => $erros];

        $dados = [];

        foreach (['nome','municipio','endereco','segmento'] as $k) {
            if (array_key_exists($k, $payload)) $dados[$k] = trim((string)$payload[$k]);
        }
        if (array_key_exists('cnpj', $payload)) $dados['cnpj'] = $this->normNull($payload['cnpj'], fn($v) => preg_replace('/\D+/', '', (string)$v));
        if (array_key_exists('cep', $payload)) $dados['cep'] = $this->normNull($payload['cep'], fn($v) => preg_replace('/\D+/', '', (string)$v));
        if (array_key_exists('ativo', $payload)) $dados['ativo'] = (int)((bool)$payload['ativo']);

        $dados['uf'] = 'SC';
        $dados['atualizado_em'] = date('c');

        try {
            $this->repo->atualizar($id, $dados);
            return ['ok' => true, 'status' => 200, 'dados' => $this->repo->buscarPorId($id)];
        } catch (PDOException $e) {
            if (str_contains(strtolower($e->getMessage()), 'unique')) {
                return ['ok' => false, 'status' => 409, 'erro' => 'Conflito: registro duplicado (ex.: cnpj já cadastrado).'];
            }
            throw $e;
        }
    }

    public function remover(int $id): array
    {
        $ok = $this->repo->remover($id);
        if (!$ok) return ['ok' => false, 'status' => 404, 'erro' => 'Empreendimento não encontrado.'];
        return ['ok' => true, 'status' => 204];
    }

    private function normNull(mixed $v, callable $map): mixed
    {
        if ($v === null) return null;
        $s = trim((string)$v);
        if ($s === '') return null;
        return $map($v);
    }
}
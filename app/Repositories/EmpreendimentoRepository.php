<?php declare(strict_types=1);

namespace App\Repositories;

use PDO;

final class EmpreendimentoRepository
{
    public function __construct(private PDO $pdo) {}

    public function criar(array $dados): int
    {
        $st = $this->pdo->prepare(
            'INSERT INTO empreendimentos (nome, cnpj, municipio, uf, endereco, cep, segmento, ativo, criado_em, atualizado_em)
             VALUES (:nome, :cnpj, :municipio, :uf, :endereco, :cep, :segmento, :ativo, :criado_em, :atualizado_em)'
        );

        $st->execute([
            ':nome' => $dados['nome'],
            ':cnpj' => $dados['cnpj'],
            ':municipio' => $dados['municipio'],
            ':uf' => $dados['uf'],
            ':endereco' => $dados['endereco'],
            ':cep' => $dados['cep'],
            ':segmento' => $dados['segmento'],
            ':ativo' => $dados['ativo'],
            ':criado_em' => $dados['criado_em'],
            ':atualizado_em' => $dados['atualizado_em'],
        ]);

        return (int)$this->pdo->lastInsertId();
    }

    public function buscarPorId(int $id): ?array
    {
        $st = $this->pdo->prepare('SELECT * FROM empreendimentos WHERE id = :id');
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function atualizar(int $id, array $dados): bool
    {
        $sets = [];
        $params = [':id' => $id];

        foreach (['nome','cnpj','municipio','uf','endereco','cep','segmento','ativo','atualizado_em'] as $k) {
            if (array_key_exists($k, $dados)) {
                $sets[] = "$k = :$k";
                $params[":$k"] = $dados[$k];
            }
        }
        if (!$sets) return false;

        $sql = 'UPDATE empreendimentos SET ' . implode(', ', $sets) . ' WHERE id = :id';
        $st = $this->pdo->prepare($sql);
        $st->execute($params);

        return $st->rowCount() > 0;
    }

    public function remover(int $id): bool
    {
        $st = $this->pdo->prepare('DELETE FROM empreendimentos WHERE id = :id');
        $st->execute([':id' => $id]);
        return $st->rowCount() > 0;
    }

    public function listar(array $filtros, int $limit, int $offset): array
    {
        $where = [];
        $params = [];

        if (!empty($filtros['nome'])) { $where[] = 'nome LIKE :nome'; $params[':nome'] = '%' . $filtros['nome'] . '%'; }
        if (!empty($filtros['municipio'])) { $where[] = 'municipio = :municipio'; $params[':municipio'] = $filtros['municipio']; }
        if (!empty($filtros['segmento'])) { $where[] = 'segmento = :segmento'; $params[':segmento'] = $filtros['segmento']; }
        if ($filtros['ativo'] !== null && $filtros['ativo'] !== '') { $where[] = 'ativo = :ativo'; $params[':ativo'] = (int)$filtros['ativo']; }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        $stCount = $this->pdo->prepare("SELECT COUNT(*) AS total FROM empreendimentos $whereSql");
        $stCount->execute($params);
        $total = (int)($stCount->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

        $st = $this->pdo->prepare(
            "SELECT * FROM empreendimentos
             $whereSql
             ORDER BY id DESC
             LIMIT :limit OFFSET :offset"
        );

        foreach ($params as $k => $v) $st->bindValue($k, $v);
        $st->bindValue(':limit', $limit, PDO::PARAM_INT);
        $st->bindValue(':offset', $offset, PDO::PARAM_INT);

        $st->execute();
        $itens = $st->fetchAll(PDO::FETCH_ASSOC);

        return ['total' => $total, 'itens' => $itens];
    }
}
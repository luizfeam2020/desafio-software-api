<?php declare(strict_types=1);

namespace App\Database;

use PDO;

final class Migrations
{
    public static function up(PDO $pdo): void
    {
        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS empreendimentos (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                cnpj TEXT,
                municipio TEXT NOT NULL,
                uf TEXT NOT NULL DEFAULT ''SC'',
                endereco TEXT,
                cep TEXT,
                segmento TEXT,
                ativo INTEGER NOT NULL DEFAULT 1,
                criado_em TEXT NOT NULL,
                atualizado_em TEXT NOT NULL
            )'
        );

        $pdo->exec(
            'CREATE UNIQUE INDEX IF NOT EXISTS idx_empreendimentos_cnpj
             ON empreendimentos(cnpj)
             WHERE cnpj IS NOT NULL'
        );

        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_empreendimentos_nome ON empreendimentos(nome)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_empreendimentos_municipio ON empreendimentos(municipio)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_empreendimentos_segmento ON empreendimentos(segmento)');
        $pdo->exec('CREATE INDEX IF NOT EXISTS idx_empreendimentos_ativo ON empreendimentos(ativo)');
    }
}
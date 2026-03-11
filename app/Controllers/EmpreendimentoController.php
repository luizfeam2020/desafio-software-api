<?php declare(strict_types=1);

namespace App\Controllers;

use App\Http\Json;
use App\Services\EmpreendimentoService;

final class EmpreendimentoController
{
    public function __construct(private EmpreendimentoService $service) {}

    public function listar(array $query): void
    {
        $res = $this->service->listar($query);
        Json::resposta($res['status'], $res['dados']);
    }

    public function buscar(int $id): void
    {
        $res = $this->service->buscar($id);
        if (!$res['ok']) { Json::resposta($res['status'], ['erro' => $res['erro']]); return; }
        Json::resposta(200, $res['dados']);
    }

    public function criar(array $payload): void
    {
        $res = $this->service->criar($payload);
        if (!$res['ok']) {
            $out = ['erro' => $res['erro']];
            if (!empty($res['campos'])) $out['campos'] = $res['campos'];
            Json::resposta($res['status'], $out);
            return;
        }
        Json::resposta(201, $res['dados']);
    }

    public function atualizar(int $id, array $payload): void
    {
        $res = $this->service->atualizar($id, $payload);
        if (!$res['ok']) {
            $out = ['erro' => $res['erro']];
            if (!empty($res['campos'])) $out['campos'] = $res['campos'];
            Json::resposta($res['status'], $out);
            return;
        }
        Json::resposta(200, $res['dados']);
    }

    public function remover(int $id): void
    {
        $res = $this->service->remover($id);
        if (!$res['ok']) { Json::resposta($res['status'], ['erro' => $res['erro']]); return; }
        Json::resposta(204, null);
    }
}
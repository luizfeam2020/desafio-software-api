<?php declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Database\Connection;
use App\Database\Migrations;
use App\Http\Request;
use App\Http\Json;
use App\Repositories\EmpreendimentoRepository;
use App\Services\EmpreendimentoService;
use App\Validators\EmpreendimentoValidator;
use App\Controllers\EmpreendimentoController;

$pdo = Connection::pdo();
Migrations::up($pdo);

$controller = new EmpreendimentoController(
    new EmpreendimentoService(
        new EmpreendimentoRepository($pdo),
        new EmpreendimentoValidator()
    )
);

$req = new Request();
$method = $req->method();
$path = $req->path();

if ($method === 'GET' && $path === '/health') {
    Json::resposta(200, ['status' => 'ok', 'servico' => 'api-empreendimentos-sc', 'versao' => '1.0.0']);
    exit;
}

if ($method === 'GET' && $path === '/api/empreendimentos') {
    $controller->listar($req->query());
    exit;
}

if ($method === 'POST' && $path === '/api/empreendimentos') {
    $controller->criar($req->json());
    exit;
}

if (preg_match('#^/api/empreendimentos/(\d+)$#', $path, $m)) {
    $id = (int)$m[1];

    if ($method === 'GET') { $controller->buscar($id); exit; }
    if ($method === 'PUT' || $method === 'PATCH') { $controller->atualizar($id, $req->json()); exit; }
    if ($method === 'DELETE') { $controller->remover($id); exit; }
}

Json::resposta(404, ['erro' => 'Rota n?o encontrada.']);

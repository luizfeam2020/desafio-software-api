\# API de Empreendimentos (SC)



API REST em PHP para cadastro e consulta de empreendimentos em Santa Catarina (SC).



\## Requisitos

\- PHP 8.2+

\- Extensões: PDO e pdo\_sqlite habilitadas



\## Como rodar

Na raiz do projeto:



```bash

php -S 127.0.0.1:8000 -t public public/router.php

```



Se o `php` não estiver no PATH no Windows, use:



```bash

C:\\sctec-desafio\\php82\\php.exe -S 127.0.0.1:8000 -t public public/router.php

```



\## Health

\*\*GET\*\* `/health`



```powershell

curl.exe -i http://127.0.0.1:8000/health

```



\## Endpoints

Base: `/api/empreendimentos`



\- POST `/api/empreendimentos`

\- GET `/api/empreendimentos`

\- GET `/api/empreendimentos/{id}`

\- PUT `/api/empreendimentos/{id}`

\- DELETE `/api/empreendimentos/{id}`



\## Exemplos (PowerShell)



Criar (POST):



```powershell

curl.exe -i -X POST "http://127.0.0.1:8000/api/empreendimentos" `

&nbsp; -H "Content-Type: application/json; charset=utf-8" `

&nbsp; -d "{`"nome`":`"Padaria do Luiz`",`"municipio`":`"Florianopolis`",`"segmento`":`"Alimentos`",`"cnpj`":`"12345678000190`",`"ativo`":1}"

```



Listar (GET):



```powershell

curl.exe -i "http://127.0.0.1:8000/api/empreendimentos?ativo=1\&limit=5\&offset=0"

```


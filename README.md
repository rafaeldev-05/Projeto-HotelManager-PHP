# HotelManager

Projeto institucional em PHP puro para estudar a arquitetura base de um sistema web: rotas, controllers, services, repositories, views e armazenamento local em JSON.

## Como rodar localmente

Requisitos:

- PHP 8.2 ou superior.

Crie ou restaure o JSON com dados iniciais:

```bash
php database/migrate.php
```

Inicie o servidor local usando a pasta publica:

```bash
php -S localhost:8000 -t public
```

Acesse:

```text
http://localhost:8000
```

## Como fazer deploy na Vercel

1. Suba este projeto para um repositorio no GitHub.
2. Na Vercel, importe o repositorio.
3. Mantenha o framework preset como `Other`.
4. Nao configure build command.
5. Publique o deploy.

A Vercel usa o arquivo `vercel.json` para executar PHP com o runtime comunitario `vercel-php@0.9.0`.

## Correcao do deploy na Vercel

Localmente o projeto funciona com `php -S localhost:8000 -t public` porque o servidor embutido do PHP usa `public/index.php` como front controller e executa o PHP antes de enviar HTML ao navegador.

Na Vercel, PHP nao e executado como Apache/Nginx tradicional. Se a entrada serverless nao estiver correta, a plataforma pode tratar o arquivo PHP como arquivo estatico ou gerar uma resposta sem o `Content-Type` esperado, o que aparece no navegador como download automatico de um arquivo pequeno chamado `download`.

A entrada serverless real deste projeto e:

```text
api/index.php
```

Esse arquivo:

- define o `Content-Type: text/html; charset=UTF-8`;
- ajusta `SCRIPT_NAME`, `PHP_SELF` e `DOCUMENT_ROOT`;
- preserva/reconstroi a rota publica recebida pela Vercel;
- carrega o front controller original em `public/index.php`.

O `vercel.json` envia somente assets para `public/assets` e manda todo o restante para `api/index.php`:

```json
{
  "version": 2,
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.9.0"
    }
  },
  "routes": [
    {
      "src": "/assets/(.*)",
      "dest": "/public/assets/$1"
    },
    {
      "src": "/(.*)",
      "dest": "/api/index.php?__route=$1"
    }
  ]
}
```

Depois do deploy, teste estas URLs:

- `/`
- `/rooms`
- `/products`
- `/reservations`
- `/reservations/create`
- `/reviews/create`
- `/assets/css/app.css`
- `/assets/js/app.js`

A home deve responder como `text/html`, e assets devem responder como CSS/JavaScript.

Se continuar baixando arquivo depois do deploy:

- confirme que o commit com `api/index.php` e `vercel.json` foi realmente publicado;
- confira se o projeto da Vercel esta usando a raiz correta do repositorio em Project Settings;
- confira se nao existe outro `vercel.json` em uma pasta acima/abaixo sendo usado no deploy;
- veja os Build Logs para confirmar que `api/index.php` aparece como Function PHP com `vercel-php@0.9.0`;
- confira Project Settings > Deployment Protection e Vercel Authentication;
- confirme que voce esta acessando o dominio de producao correto, nao um deploy antigo.

Para diagnostico temporario, defina a variavel de ambiente `VERCEL_DEBUG=1` na Vercel e acesse `/debug-vercel`. Essa rota mostra se o PHP foi executado, qual `REQUEST_URI` chegou na function e qual `public/index.php` foi carregado. Remova a variavel depois do teste.

## Papel do `api/index.php`

Na Vercel, PHP precisa rodar como Serverless Function. O arquivo `api/index.php` e o ponto de entrada da function e apenas carrega o front controller original em `public/index.php`.

O fluxo continua o mesmo:

- `public/index.php` inicializa autoload, helpers, config e rotas.
- `routes/web.php` define as URLs da aplicacao.
- Controllers renderizam views em `resources/views`.

## Papel do `vercel.json`

O `vercel.json` configura:

- `functions`: usa `vercel-php@0.9.0` para arquivos PHP dentro de `api/`.
- `routes`: primeiro deixa a Vercel servir arquivos estaticos existentes, como `/assets/css/app.css` e `/assets/js/app.js`; depois envia qualquer outra URL para `/api/index.php`.

Essa ordem e importante para CSS, JS e imagens continuarem carregando sem passar pelo roteador PHP.

## Estrutura principal

```text
api/             Entrada serverless para Vercel
app/             Controllers, services, repositories e core da aplicacao
config/          Configuracoes
database/        Migration e seed
public/          Front controller local e assets publicos
resources/views/ Telas HTML em PHP
routes/          Definicao das URLs
storage/         Banco JSON local
vercel.json      Configuracao da Vercel
```

## Rotas e assets

As rotas da aplicacao ficam em `routes/web.php`, incluindo:

- `/`
- `/rooms`
- `/products`
- `/reservations`
- `/reservations/create`
- `/reviews/create`

Os assets publicos ficam em `public/assets` e sao referenciados pelas views como `/assets/...`.

## Vercel Analytics

Este projeto foi verificado e nao usa Next.js, React, Vite ou frontend com Node:

- nao existe `package.json`;
- nao existe `app/layout.tsx`;
- nao existe `pages/_app.tsx`;
- nao existe `src/main.tsx`;
- nao existe `src/App.tsx`;
- nao existem arquivos `.tsx` ou `.jsx`.

Por isso, o pacote `@vercel/analytics` e o componente `<Analytics />` nao foram instalados. Eles se aplicam a projetos React/Next.js e adicionariam dependencias desnecessarias aqui.

Para PHP puro, o layout em `resources/views/layouts/app.php` tem suporte opcional ao script HTML do Vercel Analytics. Para ativar:

1. No painel da Vercel, abra o projeto e habilite Analytics.
2. Use a opcao de instalacao para HTML/Other frameworks e copie o caminho do script, por exemplo `/<unique-path>/script.js`.
3. Crie a variavel de ambiente `VERCEL_ANALYTICS_SCRIPT_SRC` na Vercel com esse valor.
4. Faca um novo deploy.

Quando `VERCEL_ANALYTICS_SCRIPT_SRC` estiver definida, o layout injeta o snippet do Vercel Analytics no final do `<body>`. Quando a variavel nao estiver definida, nenhum script de analytics e renderizado.

Limitacao: o Analytics depende das rotas internas criadas pela Vercel depois que a aba Analytics e habilitada. Se o painel continuar sem dados, habilite Analytics, faca redeploy e confira no navegador se existe uma requisicao para `/<unique-path>/view`.

## Observacoes sobre Vercel e PHP

- A Vercel nao executa PHP como Apache ou Nginx tradicional.
- O deploy usa Serverless Functions com `vercel-php`.
- O filesystem da function nao deve ser tratado como persistente.
- Em ambiente Vercel, o JSON e copiado de `storage/hotelmanager.json` para `/tmp/hotelmanager.json` para permitir escrita durante a execucao. Esses dados podem ser perdidos quando a instancia serverless for reciclada.
- Para producao real, o ideal e trocar o armazenamento JSON por um banco externo.

## Sobre o download automatico

Nao foi encontrado header PHP forcando download, como `Content-Disposition: attachment`, `Content-Type: application/octet-stream` ou `readfile()`.

A causa provavel do arquivo `download` era a Vercel recebendo um projeto PHP sem entrada serverless configurada. Com `api/index.php`, `vercel.json` e runtime `vercel-php`, a URL principal passa a renderizar HTML pela function PHP.

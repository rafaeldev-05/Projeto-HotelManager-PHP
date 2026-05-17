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

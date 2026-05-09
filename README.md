# HotelManager

Projeto institucional em PHP puro para estudar a arquitetura base de um sistema web: rotas, controllers, services, repositories, views e armazenamento local em JSON.

## O que este projeto ensina

- Entrada da aplicacao por `public/index.php`.
- Rotas centralizadas em `routes/web.php`.
- Controllers para receber requisicoes HTTP.
- Services para concentrar regras de negocio.
- Repositories para isolar o acesso aos dados.
- Views PHP simples em `resources/views`.
- JSON local com migration/seed em `database/migrate.php`, sem depender de extensoes extras.

## Requisitos

- PHP 8.2 ou superior.

## Como rodar

1. Crie o banco com dados iniciais:

```bash
php database/migrate.php
```

2. Inicie o servidor local:

```bash
php -S localhost:8000 -t public
```

3. Acesse:

```text
http://localhost:8000
```

## Arquitetura

```text
app/
  Controllers/   Recebem HTTP, chamam services/repositories e escolhem a view
  Core/          Infraestrutura pequena: Router, JsonStore, Controller, View
  Models/        Constantes e nomes importantes do dominio
  Repositories/  Acesso aos dados
  Services/      Regras de negocio do hotel
config/          Configuracoes
database/        Migration e seed
public/          Pasta publica do servidor
resources/views/ Telas HTML em PHP
routes/          Definicao das URLs
storage/         Banco JSON local
```

## Fluxos implementados

- Cadastro e listagem de quartos.
- Cadastro e listagem de produtos/servicos.
- Criacao de reserva pendente.
- Confirmacao ou cancelamento por pagamento da taxa.
- Check-in apenas na data de entrada.
- Registro de consumo apenas em hospedagem.
- Check-out com calculo de diarias + consumos.
- Quarto vai para limpeza apos check-out aprovado.
- Conclusao de limpeza libera o quarto.
- Cancelamento antes da hospedagem.
- Marcacao de nao compareceu.
- Avaliacao somente para reserva finalizada, com nota de 1 a 5.

## Onde estudar primeiro

1. `public/index.php`: ponto de entrada.
2. `routes/web.php`: mapa das URLs.
3. `app/Controllers/ReservationController.php`: fluxo web principal.
4. `app/Services/ReservationService.php`: regras de negocio.
5. `app/Repositories/ReservationRepository.php`: persistencia do dominio.

## Por que JSON e nao SQLite?

O PHP instalado nesta maquina esta sem `pdo_sqlite`. Para o projeto rodar agora, a persistencia foi feita em `storage/hotelmanager.json`. Em um projeto real, voce trocaria somente os repositories por SQL/MySQL/SQLite mantendo controllers, services e views quase iguais.

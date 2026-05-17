<?php
$appName = 'HotelManager';
$vercelAnalyticsScript = getenv('VERCEL_ANALYTICS_SCRIPT_SRC') ?: '';
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? $appName) ?> - <?= e($appName) ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
    <aside class="sidebar">
        <a class="brand" href="/">
            <span class="brand-mark">HM</span>
            <span>
                <strong>HotelManager</strong>
                <small>Reservas e hospedagem</small>
            </span>
        </a>
        <nav>
            <a href="/">Dashboard</a>
            <a href="/reservations">Reservas</a>
            <a href="/rooms">Quartos</a>
            <a href="/products">Produtos</a>
            <a href="/reviews/create">Avaliacoes</a>
        </nav>
    </aside>

    <main class="page">
        <header class="topbar">
            <div>
                <p class="eyebrow">Sistema institucional em PHP</p>
                <h1><?= e($title ?? $appName) ?></h1>
            </div>
            <a class="button primary" href="/reservations/create">Nova reserva</a>
        </header>

        <?php if (!empty($_GET['success'])): ?>
            <div class="alert success"><?= e($_GET['success']) ?></div>
        <?php endif; ?>

        <?php if (!empty($_GET['error'])): ?>
            <div class="alert error"><?= e($_GET['error']) ?></div>
        <?php endif; ?>

        <?php require $viewPath; ?>
    </main>

    <script src="/assets/js/app.js"></script>
    <?php if ($vercelAnalyticsScript !== ''): ?>
        <script>
            window.va = window.va || function () {
                (window.vaq = window.vaq || []).push(arguments);
            };
        </script>
        <script defer src="<?= e($vercelAnalyticsScript) ?>"></script>
    <?php endif; ?>
</body>
</html>

<?php
$status = $reservation['status'];
$days = max(1, (new DateTimeImmutable($reservation['check_out']))->diff(new DateTimeImmutable($reservation['check_in']))->days);
?>

<section class="grid two">
    <article class="panel">
        <div class="section-title">
            <h2><?= e($reservation['code']) ?></h2>
            <span class="badge"><?= e($status) ?></span>
        </div>
        <dl class="details">
            <div><dt>Hospede</dt><dd><?= e($reservation['guest_name']) ?></dd></div>
            <div><dt>Contato</dt><dd><?= e($reservation['email']) ?> / <?= e($reservation['phone']) ?></dd></div>
            <div><dt>Quarto</dt><dd><?= e($reservation['room_number']) ?> - <?= e($reservation['room_type']) ?></dd></div>
            <div><dt>Periodo</dt><dd><?= e($reservation['check_in']) ?> a <?= e($reservation['check_out']) ?> (<?= e((string) $days) ?> diaria)</dd></div>
            <div><dt>Pessoas</dt><dd><?= e((string) $reservation['people']) ?> de <?= e((string) $reservation['capacity']) ?></dd></div>
            <div><dt>Taxa de reserva</dt><dd><?= money($reservation['reservation_fee']) ?></dd></div>
        </dl>
    </article>

    <article class="panel">
        <h2>Acoes da reserva</h2>

        <form class="action-box" method="post" action="/reservations/<?= e((string) $reservation['id']) ?>/confirm">
            <strong>Pagamento da taxa</strong>
            <div class="inline-form">
                <select name="method"><option>PIX</option><option>Cartao</option></select>
                <select name="payment_status"><option>Aprovado</option><option>Recusado</option></select>
                <button class="button">Confirmar</button>
            </div>
        </form>

        <form class="action-box" method="post" action="/reservations/<?= e((string) $reservation['id']) ?>/check-in">
            <strong>Check-in</strong>
            <button class="button">Realizar check-in</button>
        </form>

        <form class="action-box" method="post" action="/reservations/<?= e((string) $reservation['id']) ?>/no-show">
            <strong>Nao compareceu</strong>
            <button class="button">Marcar no-show</button>
        </form>

        <form class="action-box danger-zone" method="post" action="/reservations/<?= e((string) $reservation['id']) ?>/cancel" data-confirm="Cancelar esta reserva?">
            <strong>Cancelamento</strong>
            <label class="check"><input type="checkbox" name="maintenance"> Quarto entrara em manutencao</label>
            <button class="button danger">Cancelar reserva</button>
        </form>
    </article>
</section>

<section class="grid two">
    <article class="panel">
        <h2>Consumos</h2>
        <form class="inline-form wrap" method="post" action="/reservations/<?= e((string) $reservation['id']) ?>/consumptions">
            <select name="product_id">
                <?php foreach ($products as $product): ?>
                    <option value="<?= e((string) $product['id']) ?>"><?= e($product['name']) ?> - <?= money($product['price']) ?></option>
                <?php endforeach; ?>
            </select>
            <input type="number" name="quantity" min="1" value="1">
            <button class="button">Adicionar consumo</button>
        </form>

        <table>
            <thead><tr><th>Item</th><th>Qtd.</th><th>Unitario</th><th>Total</th></tr></thead>
            <tbody>
            <?php foreach ($consumptions as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td><?= e((string) $item['quantity']) ?></td>
                    <td><?= money($item['unit_price']) ?></td>
                    <td><?= money($item['quantity'] * $item['unit_price']) ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$consumptions): ?>
                <tr><td colspan="4">Nenhum consumo registrado.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </article>

    <article class="panel checkout">
        <h2>Check-out</h2>
        <dl class="details">
            <div><dt>Diarias</dt><dd><?= e((string) $days) ?> x <?= money($reservation['daily_rate']) ?></dd></div>
            <div><dt>Total estimado</dt><dd><strong><?= money($total) ?></strong></dd></div>
        </dl>
        <form class="form" method="post" action="/reservations/<?= e((string) $reservation['id']) ?>/check-out">
            <label>Metodo
                <select name="method"><option>PIX</option><option>Cartao</option></select>
            </label>
            <label>Status do pagamento
                <select name="payment_status"><option>Aprovado</option><option>Recusado</option></select>
            </label>
            <button class="button primary">Finalizar hospedagem</button>
        </form>
        <a class="button ghost" href="/reviews/create">Registrar avaliacao depois do check-out</a>
    </article>
</section>


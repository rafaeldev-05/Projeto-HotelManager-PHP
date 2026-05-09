<section class="stats-grid">
    <article class="stat"><span>Quartos</span><strong><?= e((string) $stats['rooms']) ?></strong></article>
    <article class="stat"><span>Em hospedagem</span><strong><?= e((string) $stats['active']) ?></strong></article>
    <article class="stat"><span>Pendentes</span><strong><?= e((string) $stats['pending']) ?></strong></article>
    <article class="stat"><span>Finalizadas</span><strong><?= e((string) $stats['finished']) ?></strong></article>
</section>

<section class="grid two">
    <div class="panel">
        <div class="section-title">
            <h2>Reservas recentes</h2>
            <a href="/reservations">Ver todas</a>
        </div>
        <table>
            <thead><tr><th>Codigo</th><th>Hospede</th><th>Periodo</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($latestReservations as $reservation): ?>
                <tr>
                    <td><a href="/reservations/<?= e((string) $reservation['id']) ?>"><?= e($reservation['code']) ?></a></td>
                    <td><?= e($reservation['guest_name']) ?></td>
                    <td><?= e($reservation['check_in']) ?> a <?= e($reservation['check_out']) ?></td>
                    <td><span class="badge"><?= e($reservation['status']) ?></span></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$latestReservations): ?>
                <tr><td colspan="4">Nenhuma reserva cadastrada ainda.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="panel">
        <div class="section-title">
            <h2>Status dos quartos</h2>
            <a href="/rooms">Gerenciar</a>
        </div>
        <div class="room-list">
            <?php foreach ($rooms as $room): ?>
                <div class="room-row">
                    <strong><?= e($room['number']) ?></strong>
                    <span><?= e($room['type']) ?></span>
                    <em><?= e($room['status']) ?></em>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="panel">
    <div class="section-title">
        <h2>Avaliacoes recentes</h2>
        <a href="/reviews/create">Registrar avaliacao</a>
    </div>
    <div class="reviews">
        <?php foreach ($reviews as $review): ?>
            <article>
                <strong><?= str_repeat('*', (int) $review['rating']) ?></strong>
                <p><?= e($review['comment'] ?: 'Sem comentario.') ?></p>
                <small><?= e($review['guest_name']) ?> - <?= e($review['code']) ?></small>
            </article>
        <?php endforeach; ?>
        <?php if (!$reviews): ?>
            <p>Nenhuma avaliacao registrada ainda.</p>
        <?php endif; ?>
    </div>
</section>


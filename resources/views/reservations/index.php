<section class="panel">
    <div class="section-title">
        <h2>Reservas</h2>
        <a class="button primary" href="/reservations/create">Nova reserva</a>
    </div>
    <table>
        <thead>
            <tr><th>Codigo</th><th>Hospede</th><th>Quarto</th><th>Periodo</th><th>Pessoas</th><th>Status</th></tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $reservation): ?>
            <tr>
                <td><a href="/reservations/<?= e((string) $reservation['id']) ?>"><?= e($reservation['code']) ?></a></td>
                <td><?= e($reservation['guest_name']) ?></td>
                <td><?= e($reservation['room_number']) ?> - <?= e($reservation['room_type']) ?></td>
                <td><?= e($reservation['check_in']) ?> a <?= e($reservation['check_out']) ?></td>
                <td><?= e((string) $reservation['people']) ?></td>
                <td><span class="badge"><?= e($reservation['status']) ?></span></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$reservations): ?>
            <tr><td colspan="6">Nenhuma reserva cadastrada ainda.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</section>


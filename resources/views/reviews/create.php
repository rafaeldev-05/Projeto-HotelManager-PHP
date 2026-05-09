<section class="grid form-and-list">
    <form class="panel form" method="post" action="/reviews">
        <h2>Nova avaliacao</h2>
        <label>Reserva finalizada
            <select name="reservation_id">
                <?php foreach ($reservations as $reservation): ?>
                    <option value="<?= e((string) $reservation['id']) ?>">
                        <?= e($reservation['code']) ?> - <?= e($reservation['guest_name']) ?> - <?= e($reservation['status']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Nota
            <select name="rating">
                <option value="5">5</option>
                <option value="4">4</option>
                <option value="3">3</option>
                <option value="2">2</option>
                <option value="1">1</option>
            </select>
        </label>
        <label>Comentario <textarea name="comment" rows="5"></textarea></label>
        <button class="button primary" type="submit">Registrar avaliacao</button>
    </form>

    <div class="panel">
        <h2>Avaliacoes registradas</h2>
        <div class="reviews stacked">
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
    </div>
</section>


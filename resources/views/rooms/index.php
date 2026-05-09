<section class="grid form-and-list">
    <form class="panel form" method="post" action="/rooms">
        <h2>Novo quarto</h2>
        <label>Numero <input name="number" required></label>
        <label>Tipo <input name="type" placeholder="Standard, Luxo..." required></label>
        <label>Capacidade <input type="number" name="capacity" min="1" required></label>
        <label>Valor da diaria <input type="number" name="daily_rate" min="0" step="0.01" required></label>
        <label>Status
            <select name="status">
                <?php foreach ($statuses as $status): ?>
                    <option value="<?= e($status) ?>"><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button primary" type="submit">Cadastrar quarto</button>
    </form>

    <div class="panel">
        <h2>Quartos cadastrados</h2>
        <table>
            <thead><tr><th>Numero</th><th>Tipo</th><th>Cap.</th><th>Diaria</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= e($room['number']) ?></td>
                    <td><?= e($room['type']) ?></td>
                    <td><?= e((string) $room['capacity']) ?></td>
                    <td><?= money($room['daily_rate']) ?></td>
                    <td>
                        <form class="inline-form" method="post" action="/rooms/<?= e((string) $room['id']) ?>/status">
                            <select name="status">
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= e($status) ?>" <?= selected($room['status'], $status) ?>><?= e($status) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="icon-button" title="Salvar status">OK</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>


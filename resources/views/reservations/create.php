<section class="panel narrow">
    <form class="form" method="post" action="/reservations">
        <h2>Dados da reserva</h2>
        <div class="grid two">
            <label>Nome do hospede <input name="guest_name" required></label>
            <label>E-mail <input type="email" name="email" required></label>
            <label>Telefone <input name="phone" required></label>
            <label>Tipo de quarto
                <select name="room_type" required>
                    <?php foreach ($roomTypes as $type): ?>
                        <option value="<?= e($type) ?>"><?= e($type) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Data de entrada <input type="date" name="check_in" value="<?= e(date('Y-m-d')) ?>" required></label>
            <label>Data de saida <input type="date" name="check_out" value="<?= e(date('Y-m-d', strtotime('+1 day'))) ?>" required></label>
            <label>Quantidade de pessoas <input type="number" name="people" min="1" value="1" required></label>
        </div>
        <button class="button primary" type="submit">Criar reserva pendente</button>
    </form>
</section>


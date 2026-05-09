<section class="grid form-and-list">
    <form class="panel form" method="post" action="/products">
        <h2>Novo produto ou servico</h2>
        <label>Nome <input name="name" required></label>
        <label>Descricao <textarea name="description" rows="4"></textarea></label>
        <label>Preco <input type="number" name="price" min="0" step="0.01" required></label>
        <label class="check"><input type="checkbox" name="available" checked> Disponivel</label>
        <button class="button primary" type="submit">Cadastrar</button>
    </form>

    <div class="panel">
        <h2>Itens cadastrados</h2>
        <table>
            <thead><tr><th>Nome</th><th>Descricao</th><th>Preco</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= e($product['name']) ?></td>
                    <td><?= e($product['description']) ?></td>
                    <td><?= money($product['price']) ?></td>
                    <td><span class="badge"><?= ((int) $product['available']) === 1 ? 'Disponivel' : 'Indisponivel' ?></span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>


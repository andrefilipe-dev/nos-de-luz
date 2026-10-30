<?php
//require_once BASE_PATH . '/app/controllers/users-filter.php';
?>

<div class="table-responsive table-container">
    <table class="table align-middle text-nowrap">
        <thead>
            <tr>
                <th>Id</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Data</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= $user['name'] ?></td>
                        <td><?= $user['email'] ?></td>
                        <td><?= date('d/m/Y', strtotime($user['date_register'])) ?></td>
                        <td><span class="badge secondary <?= ($user['status'] === 'Ativo') ? 'green' : 'red' ?>"><?= $user['status'] ?></span></td>
                        <td><a class="btn secondary" href="<?= BASE_URL ?>dashboard/user-edit.php?id=<?= $user['id'] ?>">Editar</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Nenhum usuário encontrado.
                    </td>
                </tr>
            <?php endif; ?>
            <!--Colocar o código PHP de filtro-->
        </tbody>
    </table>
</div>
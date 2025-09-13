<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\User> $users
 */
?>
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bolder">Users</h1>
    </div>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><?= $this->Html->link('Users List', ['controller' => 'Users', 'action' => 'index'], ['class' => 'text-decoration-none text-dark']) ?></h3>
            <div>
                <?= $this->Html->link(
                    '<i class="bi bi-archive"></i> Archived Users',
                    ['action' => 'archived'],
                    ['class' => 'btn btn-secondary', 'escape' => false]
                ) ?>
            </div>
        </div>
    </div>

    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('userFeedback');?>
    </div>

    <div class="dropdown-filter">
        <div class="filter-row">
            <div class="list-add-new">
                <?= $this->Html->link(
                    '<i class="bi bi-plus-lg"></i>Add New User',
                    ['action' => 'add'],
                    ['class'=> "add-button fw-bold", 'escape' => false]
                ) ?>
            </div>
            <p class="fw-bold filter-col">Filter By:</p>
            <div class="filter-col">
                <p class="filter-label">User Type:</p>
                <?= $this->Form->control('user_type', [
                    'id' => 'type-filter',
                    'type' => 'select',
                    'label' => false,
                    'options' => ['' => 'All', 'Admin' => 'Admin', 'Customer' => 'Customer'],
                    'class' => 'type-filter-dropdown',
                    'value' => isset($typeFilter) ? $typeFilter : ''
                ]) ?>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table id="UsersTable" class="display responsive nowrap">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('first_name') ?></th>
                    <th><?= $this->Paginator->sort('last_name') ?></th>
                    <th><?= $this->Paginator->sort('user_type') ?></th>
                    <th><?= $this->Paginator->sort('created') ?></th>
                    <th class="actions" style="color: var(--text-label-color); font-weight: 500"></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="users-row" data-type="<?= h($user->user_type) ?>">
                    <td><?= $this->Number->format($user->id) ?></td>
                    <td><?= h($user->email) ?></td>
                    <td><?= h($user->first_name) ?></td>
                    <td><?= h($user->last_name) ?></td>
                    <td><?= h($user->user_type) ?></td>
                    <td><?= h($user->created) ?></td>
                    <td class="actions">
                        <?= $this->Html->link('<i class="bi bi bi-eyeglasses"></i>', ['action' => 'view', $user->id], ['class' => 'btn btn-view btn-large', 'title' => 'View', 'escape'=>false]) ?>
                        <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['action' => 'edit', $user->id], ['class' => 'btn btn-edit btn-large', 'title' => 'Edit', 'escape'=>false]) ?>
                        <?php if (!$user->archived): ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-archive"></i>',
                                ['action' => 'archive', $user->id],
                                [
                                    'confirm' => __('Are you sure you want to archive {0}?', $user->email),
                                    'class' => 'btn btn-archive btn-large',
                                    'title' => 'Archive',
                                    'escape' => false
                                ]
                            ) ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!--DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        //use datatables
        $('#UsersTable').DataTable(
            {responsive: true}
        );

        //javascript filter
        function applyFilters() {
            let typeFilter = $('#type-filter').val();

            $('.users-row').each(function () {
                let userType = $(this).data('type')?.toString().trim();
                let matchType = (typeFilter === "" || typeFilter === userType);
                $(this).toggle(matchType);
            });
        }

        $('#type-filter').on('input change', function () {
            applyFilters();
        });

        // Apply filters on page load
        applyFilters();

        // Prevent automatic scrolling when page reloads
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
    });
</script>

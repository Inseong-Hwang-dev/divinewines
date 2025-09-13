<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\WineStyle> $wineStyles
 */
?>
<div class="container p-2">
    <h3 class="admin-headings">Wine Styles</h3>
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('styleFeedback');?>
    </div>

    <div class="actions-row">
        <div class="list-add-new">
            <?= $this->Html->link(
                '<i class="bi bi-plus-lg"></i>Add New Style',
                ['action' => 'add'],
                ['class'=> "add-button", 'escape' => false],
            ) ?>
        </div>
        <div>
            <?= $this->Html->link(
                '<i class="bi bi-archive"></i> Archived Styles',
                ['action' => 'archived'],
                ['class' => 'btn btn-secondary', 'escape' => false]
            ) ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="StylesTable" class="display responsive nowrap">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('name') ?></th>
                    <th><?= $this->Paginator->sort('modified') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($wineStyles as $wineStyle): ?>
                <tr>
                    <td><?= h($wineStyle->name) ?></td>
                    <td data-order="<?= h($wineStyle->modified->format('Y-m-d H:i:s')) ?>">

                        <?= $this->Time->format($wineStyle->modified, 'dd/MM/YYYY hh:mm a') ?>
                    </td>
                    <td class="actions">
                        <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['action' => 'edit', $wineStyle->id], ['class' => 'btn btn-edit btn-large', 'title' => 'Edit', 'escape'=>false] ) ?>

                        <?php if (!$wineStyle->archived): ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-archive"></i>',
                                ['action' => 'archive', $wineStyle->id],
                                [
                                    'confirm' => __('Are you sure you want to archive {0}?', $wineStyle->name),
                                    'class' => 'btn btn-archive btn-large',
                                    'title' => 'Archive',
                                    'escape' => false
                                ]
                            ) ?>
                        <?php elseif ($wineStyle->archived): ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-archive-fill"></i>',
                                ['action' => 'restore', $wineStyle->id],
                                [
                                    'confirm' => __('Are you sure you want to restore {0}?', $wineStyle->name),
                                    'class' => 'btn btn-archive btn-large',
                                    'title' => 'Restore',
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/2.3.0/css/dataTables.dataTables.min.css">
<script src="https://cdn.datatables.net/2.3.0/js/dataTables.min.js"></script>

<script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.dataTables.min.css">


<script>
    $('#StylesTable').DataTable(
        {   responsive: true,
            autoWidth: false,
            order: [[5,'desc']],
            columnDefs: [
                { targets: [0], width: '300px' },
                { targets: [1], width: '300px' },
                { targets: [2], width: '600px' },
            ]
        }
    );
</script>

<style>
    table.dataTable td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;

    }
</style>

<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Newsletter> $newsletters
 */

?>
<div class="newsletters index content p-4">
    <h3 class="admin-headings">NewsLetters</h3>
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('newsletterFeedback');?>
    </div>

    <div class="actions-row">
        <div class="list-add-new">
            <?= $this->Html->link(
                '<i class="bi bi-plus-lg"></i>Add New Newsletter',
                ['action' => 'add'],
                ['class'=> "add-button", 'escape' => false],
            ) ?>
        </div>
    </div>

<!--    Newsletters table section-->
    <div class="table-responsive">
        <table id="NewslettersTable" class="display responsive nowrap">
            <thead>
            <tr>
                <th style="color: var(--secondary-products-color)">Edition</th>
                <th><?= $this->Paginator->sort('title') ?></th>
                <th><?= $this->Paginator->sort('subheading') ?></th>
                <th><?= $this->Paginator->sort('created_at') ?></th>
                <th><?= $this->Paginator->sort('modified_at') ?></th>
                <th><?= $this->Paginator->sort('date_published') ?></th>
                <th><?= $this->Paginator->sort('status') ?></th>
                <th> </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($newsletters as $newsletter): ?>
                <tr>
                    <td><?= h($newsletter->id) ?></td>
                    <td><?= h($newsletter->title) ?></td>
                    <td><?= h($newsletter->subheading) ?></td>

                    <td data-order="<?= h($newsletter->created_at->format('Y-m-d H:i:s')) ?>">
                        <?= $this->Time->format($newsletter->created_at, 'dd/MM/yyyy hh:mm a') ?>
                    </td>

                    <td data-order="<?= h($newsletter->modified_at->format('Y-m-d H:i:s')) ?>">
                        <?= $this->Time->format($newsletter->modified_at, 'dd/MM/yyyy hh:mm a') ?>
                    </td>

                    <td data-order="<?= h($newsletter->date_published ? $newsletter->date_published->format('Y-m-d') : '') ?>">
                        <?= $this->Time->format($newsletter->date_published, 'dd/MM/yyyy') ?>
                    </td>
                    <td><?= h($newsletter->status) ?></td>
<!--                    button section-->
                    <td class="actions">
                        <?= $this->Html->link(
                            '<i class="bi bi-eyeglasses"></i>',
                            ['action' => 'view', $newsletter->id],
                            ['class' => 'btn btn-view btn-large', 'title' => 'View', 'escape' => false]
                        ) ?>
                        <?php if($newsletter->status ==='published'):
                            echo $this->Html->link('<i class="bi bi-file-earmark-arrow-up"></i>', ['action' => 'publishNewsletter', $newsletter->id], ['class' => 'btn btn-status-unlisted btn-large',
                                'title'=>'Unpublish',
                                'escape' => false]);
                            ?>
                        <?php elseif($newsletter->status ==='unpublished'):
                            echo $this->Html->link('<i class="bi bi-file-earmark-arrow-up"></i>', ['action' => 'publishNewsletter', $newsletter->id], ['class' => 'btn btn-status-listed btn-large',
                                'title'=>'Publish',
                                'escape' => false]);
                        endif;
                        ?>
                        <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['action' => 'edit', $newsletter->id], ['class' => 'btn btn-edit btn-large', 'title' => 'Edit', 'escape'=>false] ) ?>
                        <?= $this->Form->postLink(
                            __('<i class="bi bi-trash3"></i>'),
                            ['action' => 'delete', $newsletter->id],
                            [
                                'method' => 'delete',
                                'confirm' => __('Are you sure you want to delete {0}?', $newsletter->name),
                                'class' => 'btn btn-delete btn-large',
                                'title' => 'Delete',
                                'escape' => false
                            ]
                        ) ?>
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

<script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.dataTables.min.css">

<script>
    $(document).ready(function() {
        $('#NewslettersTable').DataTable({
            responsive: {
                details: {
                    type: 'inline',
                    target: 0
                }
            },
            columnDefs: [
                {
                    targets: [0,1],
                    className: 'all'
                },
                {
                    targets: [1, 2, 3, 4, 5, 6],
                    className: 'min-tablet'
                }
            ],

            autoWidth: false,

            order: [[0,'desc']],
        });
    });
</script>

<?= $this->Html->css('admin-table.css') ?>


<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\ContactEnquiry> $contactEnquiries
 */
//echo $this->Html->css(['cake', 'fonts', 'milligram.min', 'normalize.min']);

?>
<div class="container" style="padding-top: 60px;">
    <h3><?= __('Contact Enquiries') ?></h3>
    <!--    render the feedback message for any successful crud functions-->
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('indexContactFeedback');?>
    </div>
    <div class="table-responsive">
        <table id="EnquiriesTable" class="display responsive">
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('first_name') ?></th>
                    <th><?= $this->Paginator->sort('last_name') ?></th>
                    <th><?= $this->Paginator->sort('email') ?></th>
                    <th><?= $this->Paginator->sort('phone_number') ?></th>
                    <th><?= $this->Paginator->sort('replied') ?></th>
                    <th><?= $this->Paginator->sort('date_sent') ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($contactEnquiries as $contactEnquiry): ?>
                    <tr style="<?= $contactEnquiry->isNotViewed() ? 'font-weight: bold;' : '' ?>">
                        <td><?= h($contactEnquiry->first_name) ?></td>
                        <td><?= h($contactEnquiry->last_name) ?></td>
                        <td><?= h($contactEnquiry->email) ?></td>
                        <td><?= h($contactEnquiry->phone_number) ?></td>

                        <?php if($contactEnquiry->replied === 'Not Replied'): ?>
                            <td class="not-replied"><?= h($contactEnquiry->replied) ?></td>
                        <?php elseif($contactEnquiry->replied === 'Replied'): ?>
                            <td><?= h($contactEnquiry->replied) ?></td>
                        <?php endif;?>

                        <td data-order="<?= h($contactEnquiry->date_sent->format('Y-m-d H:i:s')) ?>">
                            <?= $this->Time->format($contactEnquiry->date_sent, 'dd/MM/YYYY hh:mm a') ?>
                        </td>

                        <td>
                            <?= $this->Html->link('<i class="bi bi-eyeglasses"></i>', ['action' => 'view', $contactEnquiry->id], ['class' => 'btn btn-view btn-large', 'title' => 'View','escape'=> false]) ?>
                        </td>

                        <td class="actions">
                            <?php if($contactEnquiry->replied === "Not Replied"):?>
                                <?= $this->Html->link('<i class="bi bi-envelope-exclamation"></i>', 'mailto:' . $contactEnquiry->email,  ['class' => 'btn btn-email btn-large', 'escape' => false, 'title' => 'Send Email']) ?>
                            <?php else:?>
                                <?= $this->Html->link('<i class="bi bi-envelope"></i>', 'mailto:' . $contactEnquiry->email,  ['class' => 'btn btn-email btn-large', 'escape' => false, 'title' => 'Send Another Email']) ?>
                            <?php endif;?>

                            <?php if($contactEnquiry->replied != 'Replied'):
                                echo $this->Html->link('Mark as Replied', ['action' => 'changeRepliedStatus', $contactEnquiry->id], ['class' => 'btn btn-edit', 'title' => 'Mark as Replied','escape'=> false]) ;
                                ?>
                            <?php elseif($contactEnquiry->replied == 'Replied'):
                                echo $this->Html->link('Mark as Not Replied', ['action' => 'changeRepliedStatus', $contactEnquiry->id], ['class' => 'btn btn-edit', 'title' => 'Mark as Not Replied','escape'=> false]) ;
                            endif;
                            ?>

                        </td>
                        <td>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash3"></i>',
                                ['action' => 'delete', $contactEnquiry->id],
                                [
                                    'class'=>'btn btn-delete btn-large',
                                    'title' =>'Delete',
                                    'escape'=> false,
                                    'method' => 'delete',
                                    'confirm' => __('Are you sure you want to delete # {0}?', $contactEnquiry->id),
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
<script src="https://cdn.datatables.net/plug-ins/2.3.0/dataRender/datetime.js"></script>

<script>
    $(document).ready(function() {
        $('#EnquiriesTable').DataTable({
            responsive: true,
            order: [[5,'desc']],
        });
    });
</script>

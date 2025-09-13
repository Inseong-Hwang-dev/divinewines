<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Review> $reviews
 */
?>
<div class="container" style="padding-top: 60px;">
    <h3><?= __('Reviews') ?></h3>
    <div class="alert alert-dismissible fade show" role="alert">
        <?= $this->Flash->render('indexReviewFeedback');?>
    </div>
    <div class="table-responsive">
        <table id="ReviewsTable" class="table table-striped table-bordered admin-table">
            <thead>
                <tr>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('id') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('product_id', 'Product') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('user_id', 'User') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('rating') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('status') ?></th>
                    <th style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= $this->Paginator->sort('created') ?></th>
                    <th class="actions" style="color: var(--secondary-products-color) !important; font-weight: 600;"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?= h($review->id) ?></td>
                        <td><?= h($review->product->name) ?></td>
                        <td><?= h($review->user->email) ?></td>
                        <td>
                            <div class="star-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= $i <= $review->rating ? 'filled' : '' ?>">★</span>
                                <?php endfor; ?>
                                <span class="rating-number">(<?= $review->rating ?>)</span>
                            </div>
                        </td>
                        <td>
                            <?php if($review->status === 'pending'): ?>
                                <span class="badge bg-warning"><?= h($review->status) ?></span>
                            <?php elseif($review->status === 'approved'): ?>
                                <span class="badge bg-success"><?= h($review->status) ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger"><?= h($review->status) ?></span>
                            <?php endif;?>
                        </td>
                        <td><?= $this->Time->format($review->created, 'dd-MM-yyyy HH:mm') ?></td>
                        <td class="actions">
                            <?= $this->Html->link('<i class="bi bi-eyeglasses"></i>', ['action' => 'view', $review->id], ['class' => 'btn btn-view btn-large', 'title' => 'View','escape'=> false]) ?>
                            <?= $this->Html->link('<i class="bi bi-pencil"></i>', ['action' => 'edit', $review->id], ['class' => 'btn btn-edit btn-large', 'title' => 'Edit','escape'=> false]) ?>
                            <?= $this->Form->postLink(
                                '<i class="bi bi-trash3"></i>',
                                ['action' => 'delete', $review->id],
                                [
                                    'class'=>'btn btn-delete btn-large',
                                    'title' =>'Delete',
                                    'escape'=> false,
                                    'method' => 'delete',
                                    'confirm' => __('Are you sure you want to delete # {0}?', $review->id),
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
<?= $this->Html->css('admin-table.css') ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#ReviewsTable').DataTable({
            responsive: true,
            order: [[5,'desc']],
            language: {
                search: "Search reviews:",
                lengthMenu: "Show _MENU_ reviews per page",
                info: "Showing _START_ to _END_ of _TOTAL_ reviews",
                emptyTable: "No reviews found"
            }
        });
    });
</script>

<style>
.star-rating {
    display: flex;
    align-items: center;
    gap: 2px;
}

.star-rating .star {
    color: #ddd;
    font-size: 18px;
}

.star-rating .star.filled {
    color: #ffd700;
}

.star-rating .rating-number {
    margin-left: 5px;
    color: var(--secondary-products-color);
    font-weight: 600;
}
</style>
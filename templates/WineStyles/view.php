<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\WineStyle $wineStyle
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Wine Style'), ['action' => 'edit', $wineStyle->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Wine Style'), ['action' => 'delete', $wineStyle->id], ['confirm' => __('Are you sure you want to delete # {0}?', $wineStyle->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Wine Styles'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Wine Style'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column column-80">
        <div class="wineStyles view content">
            <h3><?= h($wineStyle->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($wineStyle->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($wineStyle->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Modified') ?></th>
                    <td><?= h($wineStyle->modified) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
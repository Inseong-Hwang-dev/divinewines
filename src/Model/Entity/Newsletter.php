<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Newsletter Entity
 *
 * @property int $id
 * @property string $title
 * @property string $subheading
 * @property string $body
 * @property string|null $image
 * @property \Cake\I18n\DateTime|null $modified_at
 * @property \Cake\I18n\DateTime|null $date_published
 * @property string|null $status
 * @property \Cake\I18n\DateTime|null $created_at
 */
class Newsletter extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'title' => true,
        'subheading' => true,
        'body' => true,
        'image' => true,
        'modified_at' => true,
        'date_published' => true,
        'status' => true,
        'created_at' => true,
    ];
}

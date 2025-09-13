<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Coupon Entity
 *
 * @property int $coupon_id
 * @property string $discount_amount
 * @property string $coupon_code
 * @property int|null $user_id
 * @property \Cake\I18n\DateTime|null $expiry_date
 * @property bool $is_active
 * @property float|null $min_purchase_amount
 * @property float|null $max_discount_amount
 * @property int|null $usage_limit
 * @property int $times_used
 *
 * @property \App\Model\Entity\User $user
 */
class Coupon extends Entity
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
        'discount_amount' => true,
        'coupon_code' => true,
        'user_id' => true,
        'expiry_date' => true,
        'is_active' => true,
        'min_purchase_amount' => true,
        'max_discount_amount' => true,
        'usage_limit' => true,
        'times_used' => true,
        'user' => true,
    ];
}

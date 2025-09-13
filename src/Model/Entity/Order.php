<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Order Entity
 *
 * @property int $order_id
 * @property int $user_id
 * @property float $total_amount
 * @property string $shipping_name
 * @property string $shipping_address1
 * @property string $shipping_address2
 * @property string $shipping_city
 * @property string $shipping_state
 * @property string $shipping_postcode
 * @property string $shipping_country
 * @property float $shipping_cost
 * @property string $status
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 *
 * @property \App\Model\Entity\User $user
 * @property \App\Model\Entity\OrderProduct[] $order_products
 */
class Order extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'order_id' => true,
        'user_id' => true,
        'total_amount' => true,
        'created' => true,
        'modified' => true,
        'user' => true,
        'order_products' => true,
        'shipping_name' => true,
        'shipping_address1' => true,
        'shipping_address2' => true,
        'shipping_city' => true,
        'shipping_state' => true,
        'shipping_postcode' => true,
        'shipping_country' => true,
        'shipping_cost' => true,
        'status'=> true,

    ];
}

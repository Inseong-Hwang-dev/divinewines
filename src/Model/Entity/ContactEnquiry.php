<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ContactEnquiry Entity
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string|null $phone_number
 * @property string $message
 * @property \Cake\I18n\Date|null $date_sent
 * @property string $replied
 * @property string $view_status
 */
class ContactEnquiry extends Entity
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
        'first_name' => true,
        'last_name' => true,
        'email' => true,
        'phone_number' => true,
        'message' => true,
        'replied' => true,
        'date_sent' => true,
        'view_status' => true,
    ];


    protected array $_virtual = ['status_label']; //Virtual field that returns a human-readable status label
    protected function _getStatusLabel(): string
    {
        $statusMap = [
            'not_viewed' => 'Not Viewed',
            'viewed' => 'Viewed',

        ];

        return $statusMap[$this->view_status] ?? 'Unknown Status';
    }

    /**
     * Check if inquiry hasn't been viewed
     */
    public function isNotViewed(): bool
    {
        return $this->view_status === 'not_viewed';
    }

    /**
     * Marks the contact as viewed and returns the modified entity
     * @return $this
     */
    public function markAsViewed()
    {
        $this->view_status = 'viewed';
        return $this; // Allows method chaining
    }
}

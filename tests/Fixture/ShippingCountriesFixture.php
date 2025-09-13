<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ShippingCountriesFixture
 */
class ShippingCountriesFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'country_code' => 'Lor',
                'country_name' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}

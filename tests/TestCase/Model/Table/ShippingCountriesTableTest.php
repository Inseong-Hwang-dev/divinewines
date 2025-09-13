<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ShippingCountriesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ShippingCountriesTable Test Case
 */
class ShippingCountriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\ShippingCountriesTable
     */
    protected $ShippingCountries;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.ShippingCountries',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ShippingCountries') ? [] : ['className' => ShippingCountriesTable::class];
        $this->ShippingCountries = $this->getTableLocator()->get('ShippingCountries', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->ShippingCountries);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\ShippingCountriesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WineStylesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WineStylesTable Test Case
 */
class WineStylesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\WineStylesTable
     */
    protected $WineStyles;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.WineStyles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('WineStyles') ? [] : ['className' => WineStylesTable::class];
        $this->WineStyles = $this->getTableLocator()->get('WineStyles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->WineStyles);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\WineStylesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\WineStylesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

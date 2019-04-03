<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\DistributorsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\DistributorsTable Test Case
 */
class DistributorsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\DistributorsTable
     */
    public $Distributors;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.distributors',
        'app.logins'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Distributors') ? [] : ['className' => DistributorsTable::class];
        $this->Distributors = TableRegistry::getTableLocator()->get('Distributors', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Distributors);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}

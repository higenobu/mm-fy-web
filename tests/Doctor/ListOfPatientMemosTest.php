<?php

namespace Tests\Doctor;

use PHPUnit\Framework\TestCase;
use App\Doctor\ListOfPatientMemos;

class ListOfPatientMemosTest extends TestCase
{
    use CommonTestConfiguration; // Use reference to shared configuration

    /**
     * Test that the class is instantiated correctly.
     */
    public function testInstantiation(): void
    {
        $config = $this->getTestConfig();

        $prefix = 'test_prefix';
        $listOfPatientMemos = new ListOfPatientMemos($prefix, $config);

        $this->assertInstanceOf(ListOfPatientMemos::class, $listOfPatientMemos);
        $this->assertEquals('test_prefix', $listOfPatientMemos->getPrefix());
    }

    /**
     * Test that default config is loaded when no custom config is passed.
     */
    public function testDefaultConfig(): void
    {
        $listOfPatientMemos = new ListOfPatientMemos('test_prefix', []);

        $this->assertNotEmpty($listOfPatientMemos->getConfig());
        $this->assertEquals('pttest', $listOfPatientMemos->getConfig()['TABLE']);
    }

    /**
     * Test that ALLOW_SORT is properly set in the configuration.
     */
    public function testAllowSortConfigurable(): void
    {
        $config = $this->getTestConfig();
        $config['ALLOW_SORT'] = false;

        $listOfPatientMemos = new ListOfPatientMemos('test_prefix', $config);
        $this->assertFalse($listOfPatientMemos->getConfig()['ALLOW_SORT']);
    }
}

<?php

namespace Tests\Doctor;

use PHPUnit\Framework\TestCase;
use App\Doctor\PatientMemoEdit;

class PatientMemoEditTest extends TestCase
{
    use CommonTestConfiguration;

    /**
     * Test that the object instantiates properly.
     */
    public function testInstantiation(): void
    {
        $config = $this->getTestConfig();

        $prefix = 'test_prefix';
        $memoEdit = new PatientMemoEdit($prefix, $config);

        $this->assertInstanceOf(PatientMemoEdit::class, $memoEdit);
    }

    /**
     * Test the anewTweak method adjusts the order date correctly.
     */
    public function testAnewTweak(): void
    {
        $config = $this->getTestConfig();

        $memoEdit = new PatientMemoEdit('test_prefix', $config);
        $memoEdit->anewTweak(null);

        $data = $memoEdit->getData(); // Assuming getData() gives us access to $data
        $this->assertArrayHasKey('OrderDate', $data);
        $this->assertEquals(date('Y-m-d'), $data['OrderDate']);
    }

    /**
     * Test the commit function adds patient information.
     */
    public function testCommit(): void
    {
        $config = $this->getTestConfig();
        $config['Patient_ObjectID'] = '12345'; // Add patient-specific data

        $memoEdit = new PatientMemoEdit('test_prefix', $config);
        $memoEdit->commit();

        $data = $memoEdit->getData();
        $this->assertEquals('12345', $data['患者']);
    }
}

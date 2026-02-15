<?php

namespace Tests\Doctor;

use PHPUnit\Framework\TestCase;
use App\Doctor\PatientMemoDisplay;

class PatientMemoDisplayTest extends TestCase
{
    use CommonTestConfiguration;

    /**
     * Test object instantiation with valid configuration.
     */
    public function testInstantiation(): void
    {
        $config = $this->getTestConfig();

        $prefix = 'test_prefix';
        $memoDisplay = new PatientMemoDisplay($prefix, $config);

        $this->assertInstanceOf(PatientMemoDisplay::class, $memoDisplay);
    }

    /**
     * Test rendering functionality of Patient Memo Display.
     */
    public function testDrawMethod(): void
    {
        $config = $this->getTestConfig();
        $memoDisplay = new PatientMemoDisplay('test_prefix', $config);

        ob_start();
        $memoDisplay->draw(); // Assuming this renders output
        $output = ob_get_clean();

        $this->assertNotEmpty($output); // Ensure display output isn’t empty
    }
}

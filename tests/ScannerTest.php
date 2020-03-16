<?php

namespace Tests\Sonar;

use PHPUnit\Framework\TestCase;

final class ScannerTest extends TestCase
{
    public function test_sonar_scanner_app_runs_properly()
    {
        $app = new \Sonar\Scanner();
        $options = new \Sonar\Options(getcwd());

        $app->run($options);

        // If we arrive here, no exception has been thrown
        $this->assertTrue(true);
    }
}

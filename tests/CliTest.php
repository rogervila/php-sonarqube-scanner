<?php

namespace Tests\Sonar;

use PHPUnit\Framework\TestCase;
use Tests\Sonar\Helpers\TestServer;
use Lead\Dir\Dir;

final class CliTest extends TestCase
{
    public function test_sonar_scanner_command_runs_properly()
    {
        $entrypoint = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'sonar-scanner';
        $server = new TestServer();

        // Start the mockup server
        $server->start();

        // Entrypoint must exist
        $this->assertTrue(file_exists($entrypoint));

        $params = ' -X -Dsonar.verbose=true -Dsonar.host.url=' . $server->getBaseUrl();
        $command = 'php ' . $entrypoint . $params;

        exec($command, $output);

        // SonarQube recieves expected parameters
        $this->assertStringContainsString($params, $output[2]);
    }

    public function test_expected_scanner_versions()
    {
        $expectedScannerVersions = 3;

        $zipfiles = Dir::scan(
            __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'scanners',
            [
                        'type' => 'file',
                        'skipDots' => true,
                        'leavesOnly' => true,
                        'followSymlinks' => true,
                        'recursive' => true,
                    ]
        );

        $this->assertCount($expectedScannerVersions, $zipfiles);
    }
}

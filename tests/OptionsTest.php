<?php

namespace Tests\Sonar;

use PHPUnit\Framework\TestCase;

final class OptionsTest extends TestCase
{
    public function test_arguments_are_parsed()
    {
        $options = new \Sonar\Options(__DIR__);

        $options->parse(['this one will be deleted', '-Dsonar.prop=something']);

        // make sure other methods can be called
        $options->setSourceManagerBranch('foo');
        $options->setEdition(123);

        $this->assertStringContainsString(
            $options->cli(),
            '-Dsonar.prop=something -Dsonar.sources=' . __DIR__ . ' -Dsonar.exclusions="vendor/**, node_modules/**, .scannerwork/**"'
        );
    }

    public function test_arguments_come_from_composer_json()
    {
        $content = [
            'name' => 'john/doe',
            'description' => 'test'
        ];

        $options = new \Sonar\Options(__DIR__);
        $options->setComposerConfiguration($content);
        $options->parse([]);

        $this->assertStringContainsString(
            $options->cli(),
            '-Dsonar.projectKey=john_doe -Dsonar.projectName=doe -Dsonar.projectDescription="test" -Dsonar.sources=' . __DIR__ . ' -Dsonar.exclusions="vendor/**, node_modules/**, .scannerwork/**"'
        );
    }
}

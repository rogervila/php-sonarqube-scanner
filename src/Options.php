<?php

namespace Sonar;

use Sonar\Contracts\DeviceDetectorInterface;
use Sonar\Values\OperatingSystem;
use Sonar\Exceptions\ZipFileNotFoundException;
use Sonar\Exceptions\UnzipFailureException;
use Sonar\Exceptions\PropertiesFileNotFoundException;
use Lead\Dir\Dir;

class Options
{
    /**
     * @var string
     */
    private $executionPath;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @param string $executionPath
     */
    public function __construct(string $executionPath)
    {
        $this->executionPath = $executionPath;
    }

    public function parse(array $arguments)
    {
        $this->arguments = $arguments;

        // var_dump($arguments);
    }
}

<?php

namespace Sonar;

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

<?php

namespace Sonar;

class Options
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
    }

    public function parse(array $arguments)
    {
        $this->arguments = $arguments;

        // var_dump($arguments);
    }
}

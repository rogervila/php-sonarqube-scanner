<?php

namespace SonarScanner\Values;

use SonarScanner\Traits\HasValue;
use SonarScanner\Exceptions\InvalidVersionException;

class Version
{
    use HasValue;

    const DEFAULT_VERSION = '3.3.0.1492';

    protected $versions = [self::DEFAULT_VERSION];

    /**
     * @param string $version
     */
    public function __construct(string $version)
    {
        if (!in_array($version, $this->versions)) {
            throw new InvalidVersionException();
        }

        $this->setValue($version);
    }

    /**
     * @return array
     */
    public function getVersions()
    {
        return $this->versions;
    }
}

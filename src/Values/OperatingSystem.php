<?php

namespace Sonar\Values;

use Sonar\Traits\HasValue;
use Sonar\Exceptions\InvalidOperatingSystemException;

class OperatingSystem
{
    use HasValue;

    const LINUX = 'linux';
    const OSX = 'macosx';
    const WINDOWS = 'windows';

    protected $list = [
        self::LINUX,
        self::OSX,
        self::WINDOWS,
    ];

    /**
     * @param string $os
     */
    public function __construct(string $os)
    {
        if (!in_array($os, $this->list)) {
            throw new InvalidOperatingSystemException();
        }

        $this->setValue($os);
    }

    public function equals(OperatingSystem $system)
    {
        return $system->value == $this->value;
    }
}

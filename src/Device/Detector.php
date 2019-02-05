<?php

namespace Sonar\Device;

use Sonar\Contracts\DeviceDetectorInterface;
use Sonar\Exceptions\InvalidOperatingSystemException;
use Sonar\Values\OperatingSystem;

class Detector implements DeviceDetectorInterface
{
    private $service;

    public function __construct()
    {
        $this->service = new \Tivie\OS\Detector();
    }

    /**
     * @return OperatingSystem
     * @throws InvalidOperatingSystemException
     */
    public function getOperatingSystem()
    {
        if ($this->service->isOSX()) {
            return new OperatingSystem(OperatingSystem::OSX);
        }

        if ($this->service->isWindowsLike()) {
            return new OperatingSystem(OperatingSystem::WINDOWS);
        }

        if ($this->service->isUnixLike()) {
            return new OperatingSystem(OperatingSystem::LINUX);
        }

        throw new InvalidOperatingSystemException();
    }
}

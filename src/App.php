<?php

namespace SonarScanner;

use SonarScanner\Contracts\DeviceDetectorInterface;
use SonarScanner\Values\Version;
use SonarScanner\Values\OperatingSystem;
use SonarScanner\Exceptions\ZipFileNotFoundException;
use SonarScanner\Exceptions\UnzipFailureException;

class App
{
    const ZIP_PREFIX = 'sonar-scanner-cli';
    const ZIP_SEPARATOR = '-';

    /**
     * @var DeviceDetectorInterface
     */
    private $detector;

    /**
     * @var Version
     */
    private $version;

    /**
     * @var ZipArchive;
     */
    private $zip;

    /**
     * @var OperatingSystem
     */
    private $os;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

    public function __construct(DeviceDetectorInterface $detector)
    {
        $this->detector = $detector;
        $this->zip = new \ZipArchive;
    }

    /**
     * @return void
     */
    public function run()
    {
        $this->version = new Version(Version::DEFAULT_VERSION);

        $this->os = $this->detector->getOperatingSystem();

        $this->setName();

        $this->setFile();

        $this->unzip();
    }

    private function setName()
    {
        $this->name = implode(self::ZIP_SEPARATOR, [
            self::ZIP_PREFIX,
            $this->version->getValue(),
            $this->os->getValue(),
        ]);
    }

    private function setFile()
    {
        $this->file = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'scanners' . DIRECTORY_SEPARATOR . $this->name . '.zip';

        if (!file_exists($this->file)) {
            throw new ZipFileNotFoundException();
        }
    }

    private function unzip()
    {
        $result = $this->zip->open($this->file);

        if ($result === true) {
            $this->zip->extractTo($this->path);
            $this->zip->close();

            $this->execute();

        } else {
            throw new UnzipFailureException();
        }
    }

    private function execute()
    {
        //
    }
}

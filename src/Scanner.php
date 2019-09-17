<?php

namespace Sonar;

use Sonar\Device;
use Sonar\Values\OperatingSystem;
use Sonar\Exceptions\ZipFileNotFoundException;
use Sonar\Exceptions\UnzipFailureException;
use Sonar\Exceptions\PropertiesFileNotFoundException;
use Lead\Dir\Dir;

class Scanner
{
    const VERSION = '4.0.0.1744';
    const FOLDER_PREFIX = 'sonar-scanner';
    const ZIP_PREFIX = 'sonar-scanner-cli';
    const FILE_SEPARATOR = '-';
    const EXTRACT_ROUTE = DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
    const EXECUTION_ROUTE = DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'sonar-scanner';

    /**
     * @var DevicedeviceInterface
     */
    private $device;

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
    private $zipName;

    /**
     * @var string
     */
    private $folderName;

    /**
     * @var string
     */
    private $zipFile;

    /**
     * @var Options
     */
    private $options;

    public function __construct()
    {
        $this->device = new Device;
        $this->zip = new \ZipArchive;
    }

    /**
     * @return void
     */
    public function run(Options $options)
    {
        $this->options = $options;

        $this->os = $this->device->detect();

        $this->setZipName();

        $this->setFolderName();

        $this->setZipFile();

        $this->unzip();

        $this->fixPermissions();

        $this->execute();
    }

    /**
     * @return void
     */
    private function setZipName()
    {
        $this->zipName = implode(self::FILE_SEPARATOR, [
            self::ZIP_PREFIX,
            self::VERSION,
            $this->os->getValue(),
        ]) . '.zip';
    }

    /**
     * @return void
     */
    private function setFolderName()
    {
        $this->folderName = implode(self::FILE_SEPARATOR, [
            self::FOLDER_PREFIX,
            self::VERSION,
            $this->os->getValue(),
        ]);
    }

    /**
     * @return void
     * @throws ZipFileNotFoundException
     */
    private function setZipFile()
    {
        $this->zipFile = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'scanners' . DIRECTORY_SEPARATOR . $this->zipName;

        if (!file_exists($this->zipFile)) {
            throw new ZipFileNotFoundException();
        }
    }

    /**
     * @return void
     * @throws UnzipFailureException
     */
    private function unzip()
    {
        if ($this->zip->open($this->zipFile) === true) {
            $this->zip->extractTo(__DIR__ . self::EXTRACT_ROUTE);
            $this->zip->close();
        } else {
            throw new UnzipFailureException();
        }
    }

    /**
     * @return void
     */
    private function fixPermissions()
    {
        echo 'Asking for executable permissions...' . PHP_EOL;

        $files = Dir::scan(
            __DIR__ . self::EXTRACT_ROUTE . $this->folderName,
            [
                'type' => 'file',
                'skipDots' => true,
                'leavesOnly' => true,
                'followSymlinks' => true,
                'recursive' => true,
            ]
        );

        foreach ($files as $file) {
            chmod($file, 0777);
        }
    }

    /**
     * @return void
     */
    private function execute()
    {
        $extension = $this->os->equals(new OperatingSystem(OperatingSystem::WINDOWS))
            ? '.bat'
            : '';

        $command = __DIR__ . self::EXTRACT_ROUTE . $this->folderName . self::EXECUTION_ROUTE . $extension . ' ' . $this->options->cli();

        echo 'Running scanner...' . PHP_EOL;
        echo 'INFO: ' . $command . PHP_EOL;

        exec($command, $output);

        echo implode(PHP_EOL, $output) . PHP_EOL;
    }
}

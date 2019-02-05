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
    const VERSION = '3.3.0.1492';
    const FOLDER_PREFIX = 'sonar-scanner';
    const ZIP_PREFIX = 'sonar-scanner-cli';
    const FILE_SEPARATOR = '-';
    const EXTRACT_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
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

    /**
     * @param Device $device
     */
    public function __construct(Device $device)
    {
        $this->device = $device;
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
            $this->zip->extractTo(self::EXTRACT_PATH);
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
        echo "Asking for executable permissions...\n";

        $files = Dir::scan(
            self::EXTRACT_PATH . $this->folderName,
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

        echo "Running scanner..." . PHP_EOL;

        exec(self::EXTRACT_PATH . $this->folderName . self::EXECUTION_ROUTE . $extension, $output);

        echo implode(PHP_EOL, $output) . PHP_EOL;
    }
}

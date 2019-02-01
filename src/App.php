<?php

namespace SonarScanner;

use SonarScanner\Contracts\DeviceDetectorInterface;
use SonarScanner\Values\Version;
use SonarScanner\Values\OperatingSystem;
use SonarScanner\Exceptions\ZipFileNotFoundException;
use SonarScanner\Exceptions\UnzipFailureException;
use SonarScanner\Exceptions\PropertiesFileNotFoundException;
use Lead\Dir\Dir;

class App
{
    const FOLDER_PREFIX = 'sonar-scanner';
    const ZIP_PREFIX = 'sonar-scanner-cli';
    const FILE_SEPARATOR = '-';
    const PROPERTIES_FILE = 'sonar-project.properties';
    const EXTRACT_PATH = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
    const EXECUTION_ROUTE = DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'sonar-scanner';

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
     * @param DeviceDetectorInterface $detector
     */
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
            $this->version->getValue(),
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
            $this->version->getValue(),
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
        $result = $this->zip->open($this->zipFile);

        if ($result === true) {
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
        if (!file_exists(getcwd() . DIRECTORY_SEPARATOR . self::PROPERTIES_FILE)) {
            throw new PropertiesFileNotFoundException();
        }

        $extension = $this->os->equals(new OperatingSystem(OperatingSystem::WINDOWS))
            ? '.bat'
            : '';

        $bin = self::EXTRACT_PATH . $this->folderName . self::EXECUTION_ROUTE . $extension;

        echo "Running scanner...\n";

        exec($bin);
    }
}

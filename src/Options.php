<?php

namespace Sonar;

class Options
{
    const EDITION_COMMUNITY = 0;
    const EDITION_DEVELOPER = 1;
    const EDITION_ENTERPRISE = 2;
    const EDITION_DATA_CENTER = 3;

    const PROPERTIES_FILE_NAME = 'sonar-project.properties';
    const INLINE_PREFIX = '-D';
    const LAUNCHER = 'sonar-scanner';

    const PROJECT_KEY = 'sonar.projectKey';
    const PROJECT_NAME = 'sonar.projectName';
    const PROJECT_DESCRIPTION = 'sonar.projectDescription';
    const SOURCES = 'sonar.sources';
    const EXCLUSIONS = 'sonar.exclusions';
    const BRANCH_NAME = 'sonar.branch.name';
    const BRANCH_TARGET = 'sonar.branch.target';

    /**
     * @var string
     */
    private $basePath;

    /**
     * @var string
     */
    private $propertiesFile;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * @var array
     */
    private $composer = [];

    /**
     * @var string
     */
    private $branch = '';

    /**
     * @var int
     */
    private $edition = self::EDITION_COMMUNITY;

    /**
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = trim($basePath);

        $this->propertiesFile = $this->basePath . DIRECTORY_SEPARATOR . self::PROPERTIES_FILE_NAME;
    }

    /**
     * @param  array $composer
     * @return void
     */
    public function setComposerConfiguration(array $composer)
    {
        $this->composer = $composer;
    }

    /**
     * @param  string $composer
     * @return void
     */
    public function setSourceManagerBranch(string $branch)
    {
        $this->branch = $branch;
    }

    /**
     * @param  int $edition
     * @return void
     */
    public function setEdition(int $edition)
    {
        $this->edition = $edition;
    }

    /**
     * @param  array $arguments
     * @return void
     */
    public function parse(array $arguments)
    {
        if (self::LAUNCHER == substr(self::LAUNCHER, -strlen(self::LAUNCHER))) {
            array_shift($arguments);
        }

        $this->arguments = $arguments;

        if (isset($this->composer['name'])) {
            $this->loadDefault(self::PROJECT_KEY, 'setProjectKeyFromComposer');
            $this->loadDefault(self::PROJECT_NAME, 'setProjectNameFromComposer');
        }

        if (isset($this->composer['description'])) {
            $this->loadDefault(self::PROJECT_DESCRIPTION, 'setProjectDescriptionFromComposer');
        }

        if (strlen($this->branch) > 0 && $this->edition > self::EDITION_COMMUNITY) {
            $this->loadDefault(self::BRANCH_NAME, 'setProjectBranchName');
            $this->loadDefault(self::BRANCH_TARGET, 'setProjectBranchTarget');
        }

        $this->loadDefault(self::SOURCES, 'setSourcesProperty');
        $this->loadDefault(self::EXCLUSIONS, 'setExclusionsProperty');
    }

    /**
     * @param  string $option
     * @param  string $method
     * @return void
     */
    private function loadDefault(string $option, string $method)
    {
        if (!$this->hasArgument(self::INLINE_PREFIX . $option)
            && !$this->propertiesFileHasOption($option)) {
            call_user_func_array([$this, $method], []);
        }
    }

    /**
     * @return string
     */
    public function cli()
    {
        return implode(' ', $this->arguments);
    }

    /**
     * @param  string  $argument
     * @return boolean
     */
    private function hasArgument(string $argument)
    {
        return count(preg_grep('/^' . str_replace('.', '\.', $argument) . '/m', $this->arguments)) > 0;
    }

    /**
     * @return bool
     */
    private function propertiesFileHasOption($option)
    {
        if (!file_exists($this->propertiesFile)) {
            return false;
        }

        preg_match('/^' . str_replace('.', '\.', $option) . '/m', file_get_contents($this->propertiesFile), $matches);

        return count($matches) > 0;
    }

    /**
     * @return void
     */
    private function setProjectKeyFromComposer()
    {
        array_push($this->arguments, self::INLINE_PREFIX . self::PROJECT_KEY . '=' . str_replace('/', '_', $this->composer['name']));
    }

    /**
     * @return void
     */
    private function setProjectNameFromComposer()
    {
        $result = explode('/', $this->composer['name']);

        if (isset($result[1])) {
            array_push($this->arguments, self::INLINE_PREFIX . self::PROJECT_NAME . '=' . $result[1]);
        }
    }

    /**
     * @return void
     */
    private function setProjectDescriptionFromComposer()
    {
        array_push($this->arguments, self::INLINE_PREFIX . self::PROJECT_DESCRIPTION . '="'. $this->composer['description'] .'"');
    }

    /**
     * @return void
     */
    private function setProjectBranchName()
    {
        array_push($this->arguments, self::INLINE_PREFIX . self::BRANCH_NAME . '=' . $this->branch);
    }

    /**
     * @return void
     */
    private function setProjectBranchTarget()
    {
        array_push($this->arguments, self::INLINE_PREFIX . self::BRANCH_TARGET . '=' . $this->branch);
    }

    /**
     * @return void
     */
    private function setSourcesProperty()
    {
        array_push($this->arguments, self::INLINE_PREFIX . self::SOURCES . '=' . $this->basePath);
    }

    /**
     * @return void
     */
    private function setExclusionsProperty()
    {
        array_push($this->arguments, self::INLINE_PREFIX . self::EXCLUSIONS . '="vendor/**, node_modules/**, .scannerwork/**"');
    }
}

<?php

namespace Sonar;

class Options
{
    const PROPERTIES_FILE_NAME = 'sonar-project.properties';
    const INLINE_PREFIX = '-D';
    const LAUNCHER = 'sonar-scanner';
    const PROJECT_KEY = 'sonar.projectKey';
    const PROJECT_NAME = 'sonar.projectName';

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
    private $arguments;

    /**
     * @var array
     */
    private $composer = [];

    /**
     * @param string $basePath
     */
    public function __construct(string $basePath)
    {
        $this->basePath = trim($basePath);

        $this->propertiesFile = $this->basePath . DIRECTORY_SEPARATOR . self::PROPERTIES_FILE_NAME;
    }

    /**
     * @return void
     */
    public function setComposerConfiguration(array $composer)
    {
        $this->composer = $composer;
    }

    /**
     * @return void
     */
    public function parse(array $arguments)
    {
        if (self::LAUNCHER == substr(self::LAUNCHER, -strlen(self::LAUNCHER))) {
            array_shift($arguments);
        }

        $this->arguments = $arguments;

        if (!$this->hasArgument(self::INLINE_PREFIX . self::PROJECT_KEY)
            && !$this->propertiesFileHasOption(self::PROJECT_KEY)
            && isset($this->composer['name'])) {
            $this->setProjectKeyFromComposer();
        }

        if (!$this->hasArgument(self::INLINE_PREFIX . self::PROJECT_NAME)
            && !$this->propertiesFileHasOption(self::PROJECT_NAME)
            && isset($this->composer['name'])) {
            $this->setProjectNameFromComposer();
        }
    }

    /**
     * @return string
     */
    public function cli()
    {
        return implode(' ', $this->arguments);
    }

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
}

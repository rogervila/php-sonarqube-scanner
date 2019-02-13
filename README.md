<p align="center"><img width="250" src="https://i.imgur.com/xcIhGwP.png" alt="Run SonarQube Scanner with composer" /></p>

[![Latest Stable Version](https://poser.pugx.org/rogervila/php-sonarqube-scanner/v/stable)](https://packagist.org/packages/rogervila/php-sonarqube-scanner)
[![Total Downloads](https://poser.pugx.org/rogervila/php-sonarqube-scanner/downloads)](https://packagist.org/packages/rogervila/php-sonarqube-scanner)
[![Build Status](https://travis-ci.org/rogervila/php-sonarqube-scanner.svg?branch=master)](https://travis-ci.org/rogervila/php-sonarqube-scanner)
[![Build status](https://ci.appveyor.com/api/projects/status/weidwo98jcdrtkxm?svg=true)](https://ci.appveyor.com/project/roger-vila/php-sonarqube-scanner)



# Run SonarQube Scanner with composer

## Usage

**Install the package as a dev requirement**

```
composer require rogervila/php-sonarqube-scanner --dev
```


**Run with composer**

```
vendor/bin/sonar-scanner
```

## Defaults

In some cases, if the package finds missing properties, it will provide them automatically.

| Property  | Source | Example
|----|---|---|
| sonar.projectKey  | adapted `composer.json` name property | `-Dsonar.projectKey=rogervila_php-sonarqube-scanner`
| sonar.projectName | adapted `composer.json` name property | `-Dsonar.projectName=php-sonarqube-scanner`

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

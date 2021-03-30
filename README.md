<p align="center"><img width="250" src="https://i.imgur.com/xcIhGwP.png" alt="Run SonarQube Scanner with composer" /></p>

[![Latest Stable Version](https://poser.pugx.org/rogervila/php-sonarqube-scanner/v/stable)](https://packagist.org/packages/rogervila/php-sonarqube-scanner)
[![Total Downloads](https://poser.pugx.org/rogervila/php-sonarqube-scanner/downloads)](https://packagist.org/packages/rogervila/php-sonarqube-scanner)
[![Build Status](https://github.com/rogervila/php-sonarqube-scanner/workflows/build/badge.svg)](https://github.com/rogervila/php-sonarqube-scanner/actions)

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

If the package finds that there are missing properties, it will try provide them automatically from your project's `composer.json` file.

| Property | Source | Example
|---|---|---|
| sonar.projectKey  | adapted `composer.json` name property | `-Dsonar.projectKey=rogervila_php-sonarqube-scanner`
| sonar.projectDescription | adapted `composer.json` description property | `-Dsonar.projectDescription="Run SonarQube Scanner with composer"`
| sonar.projectName | adapted `composer.json` name property | `-Dsonar.projectName=php-sonarqube-scanner`
| sonar.sources | Base project path | `-Dsonar.sources=<PROJECT PATH>`
| sonar.exclusions | Opininated exclusions based on composer projects usage | `-Dsonar.exclusions="vendor/**, node_modules/**, .scannerwork/**"`

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

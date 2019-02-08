<p align="center"><img src="https://i.imgur.com/xcIhGwP.png" alt="Run SonarQube Scanner with composer" /></p>

[![Build Status](https://travis-ci.org/rogervila/php-sonarqube-scanner.svg?branch=master)](https://travis-ci.org/rogervila/php-sonarqube-scanner)
[![Build status](https://ci.appveyor.com/api/projects/status/weidwo98jcdrtkxm?svg=true)](https://ci.appveyor.com/project/roger-vila/php-sonarqube-scanner)


# Run SonarQube Scanner with composer

**Install the package as a dev requirement**

```
composer install rogervila/php-sonarqube-scanner --dev
```

> Make sure you have a `sonar-project.properties` on your project root!


**Run with composer**

```
vendor/bin/sonar-scanner
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

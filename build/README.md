# Multi-Version Development

*by Niels Braczek, (C) 2013-15 All rights reserved.*

This document describes a standardized layout and process for development of multi-version
extensions. It supports

  - integration tests on different Joomla! versions
  - documentation generation
  - quality measuring

Although this environment is developed for and only tested with Joomla!, it is not restricted
to that CMS.

*Breklum, July 2013*

## Installation

### Prerequisites

In order to use this build and test environment, you need to have

  - [Composer][composer]
  - [Docker][docker]
  - [Docker-Compose][fig]

properly installed on your development system. Everything else will be retrieved automatically when it is needed.

[composer]: https://getcomposer.org/ "Dependency Manager for PHP"
[docker]: https://www.docker.com/ "Container Virtualization"
[fig]: https://www.docker.com/ "Docker Orchestration Tool"


### Preparation of the build and test environment

Download the build environment and unpack it into your project directory.
In the `build` subdirectory, call Composer to resolve the dependencies.

    $ cd <project>/build
    $ composer install

## Usage

The Phing build file `build.xml` located in the `build` directory provides a number of useful build targets.
Most of these targets are implemented in separate files, which can be found in the `build/phing` directory.
 
### Docker

#### docker-build

This is one of the most complex targets. Depending on the environment definitions read from `tests/servers`,
it will download the requested Joomla! versions and prepare any Docker container needed to run them on the requested
web server and database combinations. The volumes and configuration files for the containers are provided in
`build/servers`, with a subdirectory for each container. A single `docker-compose.yml` is created in the project
root directory to manage and link the containers.

#### docker-rm

Remove the containers, which where built with `docker-build`.

#### docker-start

Build the containers and run them in the background.

#### docker-stop

Stop and remove the containers.

## Directory Layouts

### Source Directory Layout

Usually, the source directory layout follows the structure of the installation package. While
that makes packaging easier, it is hard to combine coverage reports from the integration tests
and the unit tests, if their directory layouts are different. The integration tests are always
run with the runtime structure.

Because of that, the source directory has the same structure as in a running installation.
For a `foobar` component, that would be

    administrator/components/com_foobar/
    administrator/language/en-GB/
    components/com_foobar/
    language/en-GB/
    media/com_foobar/

Unused directories can of course be omitted.
All installation related files are located in the `installation` directory.

### Test Directory Layout

The test directory layout follows the same structure as the source directory for integration
and unit tests.

    integration
        administrator/components/com_foobar/
        components/com_foobar/
    unit
        administrator/components/com_foobar/
        components/com_foobar/

The integration tests are copied to prepared test installations of different Joomla! versions
and run there. The unit tests are run where they are, and can thus only contain tests, that do
not need the CMS, framework or platform in any way.

### Test Installations

The test installations need a certain layout, too. For each installation, a `source` and a
`tests` directory are created. The `source` directory contains the Joomla! installation.
It is recommended to name the root directories with the version number, e.g. `J3.1.1`.
To reduce changes due to new versions, symlinks should be created, e.g. `J3-latest` pointing
to the newest 3.x.x installation of Joomla!.

PHPUnit on a test installation must be configured to create `clover.xml` and `junit.xml`
in the `build/logs` directory. These log files will be used for a consolidated test report.

## Quality Check

The [PHP Quality Assurance Toolchain][phpqatools] contains a lot of tools for creating metrics
and documentation. It can be easily installed using PEAR.

This development environment uses

  - PHPUnit
  - PHPLOC
  - PHP Mess Detector
  - PHP Copy/Paste Detector
  - PHP_CodeSniffer
  - PHP_CodeBrowser
  - PHP_Depend

Some of them produce XML log files, which are stored in `build/logs`. Additionally, the test
logs from the test installations are are moved to this directory, grouped by the name of the
test installation. After a full build, the following log files are found:

    J3-latest       // one for each test installation, named accordingly
        clover.xml
        junit.xml
    checkstyle.xml  // Style violations report from PHP_CodeSniffer
    clover.xml      // Coverage report from PHPUnit
    depend.xml      // Dependency metrics from PHP_Depend
    junit.xml       // Test report from PHPUnit
    phploc.csv      // LOC based metrics from PHPLOC
    pmd.xml         // Mess report from PHP Mess Detector
    pmd-cpd.xml     // Code duplication report from PHP Copy/Paste Detector
    summary.xml     // Several other metrics from PHP_Depend

[phpqatools]: http://phpqatools.org/ "PHP QA Tools project home"

## Documentation

Documentation is created during the build task.

### Changelog

A changelog is created from the commit history of `git`.

    file://<your-project's-root>/CHANGELOG.md

### API Documentation

For API documentation, either [PHPDocumentor2][phpdoc] or [ApiGen][apigen] can be used. The
documentation is located at

    file://<your-project's-root>/build/api/index.html

[apigen]: http://apigen.org/ "ApiGen project home"
[phpdoc]: http://www.phpdoc.org/ "PHPDocumentor2 project home"

### Quality Report

The [PHP_CodeBrowser][phpcb] aggregates the log files from the QA toolchain with the source
code into a browsable quality report. It is located at

    file://<your-project's-root>/build/code-browser/index.html

[phpcb]: http://github.com/Mayflower/PHP_CodeBrowser "PHP_CodeBrowser on GitHub"

### Coverage Report

The test coverage report is located at

    file://<your-project's-root>/build/coverage/index.html

**Currently, it only shows the coverage by the unit tests.
It is planned to pull the information from the integration tests.**

### Charts

[PHPDepend][pdepend] creates two charts,

    file://<your-project's-root>/build/charts/dependencies.svg
    file://<your-project's-root>/build/charts/overview-pyramid.svg

[pdepend]: http://pdepend.org/ "PHP Depend project home"

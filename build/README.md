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

  - [Composer][composer], a dependency manager for PHP
  - [Docker][docker] for lightweight container virtualization
  - [Docker-Compose][fig], a Docker orchestration tool
  - [DNSmasq][dnsmasq], a local lightweight DNS server
  - [GraphViz][graphviz] for diagrams in the API documentation

properly installed on your development system. Everything else will be retrieved automatically when it is needed.

[composer]: https://getcomposer.org/ "Dependency Manager for PHP"
[docker]: https://www.docker.com/ "Container Virtualization"
[fig]: https://www.docker.com/ "Docker Orchestration Tool"
[dnsmasq]: http://www.thekelleys.org.uk/dnsmasq/doc.html "Local DNS Server"
[graphviz]: http://www.graphviz.org/ "Graph Visualization Software"

### Configuration of DNSmasq

To route all requests for `*.dev` domains to localhost, add the line

    address=/dev/127.0.0.1
    
to the DNSmasq configuration. This is best done in its own file named `dev` in the `/etc/dnsmasq.d` directory (for Ubuntu).
You may add it to `/etc/dnsmasq.conf` directly instead, if you want. It is set up correctly, if the output of

    dig *.dev
    
contains the lines

    ;; ANSWER SECTION:
    *.dev.			0	IN	A	127.0.0.1

### Preparation of the build and test environment

Download the build environment and unpack it into your project directory.
In the `build` subdirectory, call Composer to resolve the dependencies.

    $ cd <project>/build
    $ composer install

## Usage

The Phing build file `build.xml` located in the `build` directory provides a number of useful build targets.
Most of these targets are implemented in separate files, which can be found in the `build/phing` directory.

In your project root directory, create a new `build.xml` file with this content:

    <?xml version="1.0" encoding="UTF-8"?>
    <project name="Name of your Project" default="build" basedir=".">
    
        <!-- Directory layout -->
        <property name="build" value="${project.basedir}/build"/>
        <property name="dist" value="${project.basedir}/dist"/>
        <property name="source" value="${project.basedir}/source"/>
        <property name="tests" value="${project.basedir}/tests"/>
    
        <!-- Package data -->
        <property name="package.type" value="com_"/>
        <property name="package.name" value="foobar"/>
        <property name="manifest.file" value="installation/manifest.xml"/>
    
        <!-- supported generators: phpdocumentor2, apigen -->
        <property name="apidoc.generator" value="apigen"/>
    
        <!-- This line does the magic -->
        <import file="${build}/build.xml"/>
    
    </project>

Of course you can and should set the property values according to your environment setup.

Then, in your project root directory, issue the command

    $ phing help
    
to list all available build targets with their description and other useful information.

## Directory Layouts

When using this build and test environment, your directory layout looks like this:

    <project>/                      # Your project's root directory
     |- build/                      # Build related files
     |   |- bin                     # Executable files, that are not (yet) available through Composer
     |   |- cache/                  # [gen] Cache for downloaded Joomla! versions
     |   |- config/                 # Configuration files 
     |   |- docker/                 # Definition of individual Docker images, that are not (yet) available from DockerHub
     |   |- logs/                   # [gen] Log files produced by the build and test environment
     |   |- phing/                  # Phing related files
     |   |   |- docs/               # Documentation for the custom Phing tasks
     |   |   |- tasks/              # Custom tasks for Phing
     |   |   |- *.xml               # Build target definitions, included by build.xml
     |   |   `- tasks.properties    # Task properties, included by build.xml
     |   |- report/                 # [gen] HTML reports generated from the log files
     |   |- servers/                # [gen] Volumes and configuration files for the Docker containers
     |   |- template/               # Templates for container files
     |   |- vendor/                 # [gen] Dependencies installed by Composer
     |   |- build.xml               # The main build file
     |   |- composer.json           # Description of the dependencies
     |   |- composer.lock           # [gen] Composer lock file
     |   |- README.md               # This file
     |   `- version.json            # [gen] Available Joomla! versions
     |- dist/                       # [gen] Your project's distribution packages
     |- docs/                       # Your project's documentation
     |- source/                     # Your project's source 
     |- tests/                      # Your project's test sources 
     `- build.xml                   # Your project's build file

The marker `[gen]` denotes files and directories that will be generated during the build process when needed.

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

    file://<your-project's-root>/build/report/coverage/index.html

It contains the aggregated coverage information from unit tests, integration tests, and system tests.

### Charts

[PHPDepend][pdepend] creates two charts,

    file://<your-project's-root>/build/charts/dependencies.svg
    file://<your-project's-root>/build/charts/overview-pyramid.svg

[pdepend]: http://pdepend.org/ "PHP Depend project home"

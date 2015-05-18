<?php
/**
 * @todo Make this a command with parameters
 */
require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/CoverageCollector.php';
$source = dirname(__DIR__) . '/logs/coverage/*.cov';
$report = dirname(__DIR__) . '/coverage';
$collection = new CoverageCollector();
foreach (glob($source) as $file)
{
	echo "Merging $file\n";
	$coverage = null;
	include $file;
	$collection->merge($coverage);
}
echo "Generating report in $report\n";
$writer = new PHP_CodeCoverage_Report_HTML;
$writer->process($collection->coverage(), $report);

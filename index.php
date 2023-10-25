<?php

require 'vendor/autoload.php';

use D7_analyzer\D7CodebaseAnalysis;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

// Specify our Twig templates location
//$loader = new Filesystemloader(__DIR__.'/assets/templates');
//// Instantiate our Twig
//$twig = new Environment($loader);
//$d7_analysis = new D7CodebaseAnalysis();
//$theme_templates = $d7_analysis->scanForThemeTemplates();
//$theme_file_scan = $d7_analysis->scanThemeFile();
//$custom_modules = $d7_analysis->scanForCustomModules(FALSE, FALSE);
//// Run code sniffer.
////$d7_analysis->runCodeSniffer('csv');
////$sniff_results_csv = $d7_analysis->getCodeSnifferResultsCsv();
//$sniff_results = $d7_analysis->sniffResultsToArray();
//// Rendering of page.
//echo $twig->render('header.html.twig');
//echo $twig->render('d7-theme-templates.html.twig', ['data' => $theme_templates]);
//echo $twig->render('custom-modules.html.twig', ['data' => $custom_modules]);
//echo $twig->render('code-sniffer.html.twig', ['data' => $sniff_results]);
//echo $twig->render('footer.html.twig');


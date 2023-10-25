<?php

// src/Command/ScanThemeTemplatesCommand.php
namespace D7_analyzer\Command;

use D7_analyzer\D7CodebaseAnalysis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class D7AnalysisReportCommand extends Command {
  protected static $defaultName = "d7-analysis-report";
  protected function configure() {
    $this
      ->setDescription('Runs a D7 analysis report.')
      ->addUsage('d7-analysis-report')
    ;
  }
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $output->writeln('');
    $d7_analysis = new D7CodebaseAnalysis();
    $general_table = new Table($output);
    $general_table->setHeaderTitle('General Information');
    $rows = [];
    $rows[] = ["Acquia Subscription Type", "Ask AM/CSM"];
    $rows[] = ["Drupal Core Version", $d7_analysis->getDrupalVersion()];
    $rows[] = ["Code Studio/Pipelines?", "Ask AM/CSM"];
    $rows[] = ["Drush Version", $d7_analysis->getDrushVersion()];
    $rows[] = ["Multisite", $d7_analysis->isThisMultisite()];
    $rows[] = ["Install Profile", $d7_analysis->getInstallProfile()];
//    $rows[] = new TableSeparator();
    $general_table->setRows($rows);
    $general_table->setHeaders(["Item", "Status"]);
    $general_table->render();
    $output->writeln('');

    $theme_table = new Table($output);
    $theme_table->setHeaderTitle('Theme Information');
    $theme_templates = $d7_analysis->scanForThemeTemplates();
    $rows = [];
    foreach ($theme_templates as $type => $templates) {
      if ($type !== 'counts') {
        continue;
      }
      foreach ($templates as $template_type => $count) {
        $rows[] = [$template_type, $count];
      }
    }
    $theme_table->setHeaders(["Type", "Count"]);
    $theme_table->setRows($rows);
    $theme_table->render();
    $output->writeln('');
    return Command::SUCCESS;
  }
  // General information.
    // Acquia Subscription Type
    // Drupal core version
    // Code Studio/Pipelines?
    // Drush version
    // Is this a multisite?
    // Installation profile
  // composer version?
  // number of patches
  // current php version
  // number of php errors
  // are there php fields in the db?
  // number of contrib modules
  // number of custom modules
  // number of views
}
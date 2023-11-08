<?php

// src/Command/ScanThemeTemplatesCommand.php
namespace D7_analyzer\Command;

use D7_analyzer\Connector\DrushConnector;
use D7_analyzer\D7CodebaseAnalysis;
use D7_analyzer\DataExtractor\DrushDataExtractor;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Yaml\Yaml;

class D7AnalysisReportCommand extends Command {
  protected static $defaultName = "d7-analysis-report";
  protected function configure() {
    $this
      ->setDescription('Runs a D7 analysis report.')
      ->addUsage('d7-analysis-report')
    ;
  }
  protected function execute(InputInterface $input, OutputInterface $output): int {
    // Load the project config.
    $project_config = file_get_contents('./config.yml');
    $config_yaml = Yaml::parse($project_config);

    // General information.
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
    $general_table->setRows($rows);
    $general_table->setHeaders(["Item", "Status"]);
    $general_table->render();

    // Theme information.
    $theme_table = new Table($output);
    $theme_table->setHeaderTitle('Theme Information');
    $theme_table->setHeaders(["Type", "Count"]);
    $theme_templates = $d7_analysis->scanForThemeTemplates();
    $rows = [];
    $total_template_count = 0;
    foreach ($theme_templates as $type => $templates) {
      if ($type !== 'counts') {
        continue;
      }
      foreach ($templates as $template_type => $count) {
        $rows[] = [$template_type, $count];
        $total_template_count += $count;
      }
    }
    $rows[] = new TableSeparator();
    $rows[] = ['total', $total_template_count];
    $theme_table->setRows($rows);
    $theme_table->render();

    // Customization information.
    $customization_table = new Table($output);
    $customization_table->setHeaderTitle('Custom Modules');
    $customization_table->setHeaders(["Module"]);
    $should_update_custom_modules = $config_yaml['update_custom_modules'] ?? FALSE;
    if ($should_update_custom_modules) {
      $output->writeln("Updating custom module list, this takes some extra time\n");
    }
    $custom_modules = $d7_analysis->scanForCustomModules(FALSE, $should_update_custom_modules);
    $rows = [];
    foreach ($custom_modules as $custom_module) {
      $rows[] = [$custom_module];
    }
    $rows[] = new TableSeparator();
    $rows[] = ['total custom modules', count($custom_modules)];
    $customization_table->setRows($rows);
    $customization_table->render();
    $output->writeln('');
    return 1;
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
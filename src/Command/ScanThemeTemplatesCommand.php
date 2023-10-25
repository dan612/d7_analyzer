<?php

// src/Command/ScanThemeTemplatesCommand.php
namespace D7_analyzer\Command;

use D7_analyzer\D7CodebaseAnalysis;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;

class ScanThemeTemplatesCommand extends Command {
  protected static $defaultName = "scan-theme-templates";
  protected function configure() {
    $this
      ->setAliases(['stt [<type>]'])
      ->setDescription('Scans for all templates within custom theme.')
      ->addUsage('scan-theme-templates node_templates')
      ->addArgument('type', InputArgument::OPTIONAL)
    ;
  }
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $get_type = $input->getArgument('type') ?? FALSE;
    $d7_analysis = new D7CodebaseAnalysis();
    $theme_templates = $d7_analysis->scanForThemeTemplates();
    $table = new Table($output);
    $rows = [];
    foreach ($theme_templates as $type => $templates) {
      if ($type == 'counts') {
        continue;
      }
      if ($get_type && $get_type == $type) {
        foreach ($theme_templates[$type] as $template) {
          $rows[] = [$type, $template];
        }
      }
      elseif (!$get_type) {
        foreach ($templates as $template) {
          $rows[] = [$type, $template];
        }
      }
    }
    $table
      ->setHeaders(['Type', 'Template'])
      ->setRows($rows);
    $table->render();
    return Command::SUCCESS;
  }
}
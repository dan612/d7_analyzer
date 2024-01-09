<?php

// src/Command/SetupCommand.php
namespace D7_analyzer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class SetupCommand extends Command {
  protected static $defaultName = "setup";
  protected $fileSystem;

  protected function configure() {
    $this
      ->setAliases(['setup'])
      ->setDescription('Sets up the Analyzer.')
      ->addUsage('setup')
//      ->addArgument('type', InputArgument::OPTIONAL) # add support for other container servs.
    ;
  }
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->fileSystem = new Filesystem();
    // Get default config.yml
    $default_config_file = './assets/setup/default.config.yml';
    if ($this->fileSystem->exists('./config.yml')) {
      $helper = $this->getHelper('question');
      $question = new ConfirmationQuestion('Config file exists, really replace? (y/n) ', false);
      if (!$helper->ask($input, $output, $question)) {
        $output->writeln("Backing out.");
        return 0;
      }
    }
    $this->fileSystem->copy($default_config_file, './config2.yml');
    $output->writeln("Copied default configuration to project root.");
    // Get default lando file.
    $default_lando_file = './assets/setup/default.lando.yml';
    if ($this->fileSystem->exists('./.lando.yml')) {
      $helper = $this->getHelper('question');
      $question = new ConfirmationQuestion('Landofile file exists, really replace? (y/n) ', false);
      if (!$helper->ask($input, $output, $question)) {
        $output->writeln("Backing out.");
        return 0;
      }
    }
    $this->fileSystem->copy($default_lando_file, './.lando.yml');
    $output->writeln("Copied default Landofile to project root.");
    $output->writeln("Adjust the new config.yml and .lando.yml in the project root.");

    return 1;
  }
}
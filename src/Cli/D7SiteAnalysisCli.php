<?php

namespace D7_analyzer\Cli;

class D7SiteAnalysisCli {

  protected $commands = [];

  public function addCommand($name, $callable) {
    $this->commands[$name] = $callable;
  }

  public function getCommand($command) {
    return $this->commands[$command] ?? $this->commands;
  }

  public function sayHi($argv = ['no args']) {
    if ($argv[0] == 'no args') {
      echo "no args";
    }
    else {
      echo "Hello $argv\n";
    }
  }

}
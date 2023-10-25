<?php

namespace D7_analyzer\Connector;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class DrushConnector {

  const DRUSH_BASE = './vendor/bin/drush';
  protected $projectRoot;
  protected $projectUri;
  protected $command;

  public function setProjectRoot($root) {
    $this->projectRoot = $root;
  }

  public function setProjectUri($uri) {
    $this->projectRoot = $uri;
  }

  public function setCommand($cmd) {
    $this->command = $cmd;
  }

  public function run() {
    $process = new Process([
      self::DRUSH_BASE,
      $this->command,
      "--root=$this->projectRoot",
      "--uri=$this->projectUri"
    ]);
    $process->run();
    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }
    echo $process->getOutput();
  }


}
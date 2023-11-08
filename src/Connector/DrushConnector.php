<?php

namespace D7_analyzer\Connector;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class DrushConnector {

  protected $drushBase = './vendor/bin/drush';
  protected $projectRoot;
  protected $projectUri;
  protected $command;
  protected $containerName;

  public function setProjectRoot($root) {
    $this->projectRoot = $root;
  }

  public function setProjectUri($uri) {
    $this->projectUri = $uri;
  }

  public function setCommand($cmd) {
    $this->command = $cmd;
  }

  public function setContainerName($name) {
    $this->containerName = $name;
  }

  public function run() {
    $cmd =
      'docker exec ' .
      "$this->containerName " .
      "$this->drushBase " .
      "$this->command " .
      "--root=$this->projectRoot --uri=$this->projectUri";
    $output = shell_exec($cmd);
//    $cmd_args = [
//      self::DRUSH_BASE,
//      $this->command,
//      "--root=$this->projectRoot",
//      "--uri=$this->projectUri"
//    ];
//    $cmd = implode(' ', $cmd_args);
//    $process = new Process([$cmd]);
//    $process->run();
//    if (!$process->isSuccessful()) {
//      throw new ProcessFailedException($process);
//    }
    return $output;

  }

}
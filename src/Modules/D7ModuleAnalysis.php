<?php

namespace D7_analyzer\Modules;

use GuzzleHttp\Client;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;

class D7ModuleAnalysis {

  public $d7ModulesDirs = [
    'sites/all/modules/custom',
    'sites/all/modules',
    'sites/all/modules/contrib'
  ];
  protected $d7ModuleList = [];
  protected $config;

  /**
   * Public constructor.
   */
  public function __construct() {
    $project_config = file_get_contents('./config.yml');
    $this->config = Yaml::parse($project_config);
    foreach ($this->d7ModulesDirs as $modules_dir) {
      $global_path_to_module = implode('/', [
        $this->config['global_project_path'],
        $this->config['web_root'],
        $modules_dir
      ]);
      $this->d7ModuleList[] = glob($global_path_to_module . '/*', GLOB_ONLYDIR);
    }
    $this->d7ModuleList = array_merge(...$this->d7ModuleList);
  }

  /**
   * Gets an array of custom modules.
   *
   * @param $limit
   *   Optional param to limit results for testing.
   *
   * @return array
   *   The custom modules.
   */
  public function getCustomModules($limit = FALSE) {
    $custom_modules = [];
    //
    if ($limit) {
      $this->d7ModuleList = array_slice($this->d7ModuleList, 1, $limit);
    }
    foreach ($this->d7ModuleList as $module_path) {
      $is_custom = $this->checkIfModuleIsCustom($module_path);
      if ($is_custom) {
        $custom_modules[] = basename($module_path);
      }
    }
    
    return $custom_modules;
  }

  /**
   * Makes a request to git repo to check if custom.
   *
   * @param $module_path
   * @return bool|void
   */
  public function checkIfModuleIsCustom($module_path) {
    $project_name = basename($module_path);
    $client = new Client(['http_errors' => false]);
    $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36';
    $headers = [
      'User-agent' => $ua,
      'allow_redirects' => false
    ];
    $project_url = "https://git.drupalcode.org/project/$project_name";
    try {
      $response = $client->request('get', $project_url, $headers);
      $code = $response->getStatusCode();
      if ($code !== 200) {
        return TRUE;
      }
      else {
        return FALSE;
      }
    }
    catch (\GuzzleHttp\Exception\GuzzleException $e) {
      echo $e->getMessage();
    }
  }

  /**
   * D7 Module list.
   *
   * @return array
   */
  public function getModuleList() {
    return $this->d7ModuleList;
  }

  /**
   * Runs code sniffer.
   *
   * @param $format
   *   The format of the output.
   * @return string
   */
  public function runPhpcs($format, $paths = FALSE) {
    $command = [
      './vendor/bin/phpcs',
      '--standard=Drupal,DrupalPractice',
      '--extensions=php,module,inc,install,test,profile,theme'
    ];
    if ($paths) {
      $scan_paths = $paths;
    }
    else {
      $scan_path = [implode('/',[
        $this->config['global_project_path'],
        $this->config['web_root'],
        'sites/all/modules/custom'
      ])];
      $scan_paths = $scan_path;
    }
    foreach ($scan_paths as $scan_path) {
      $command[] = $scan_path;
    }
    $report_file = "./reports/phpcs_report.{$format}";
    $command[] = "--report-file=$report_file";
    if ($format == 'csv') {
      $command[] = '--report=csv';
    }
    $process = new Process($command);
    $process->run();
    
    return "Report written to $report_file";
  }

}
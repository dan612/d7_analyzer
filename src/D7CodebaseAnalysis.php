<?php

namespace D7_analyzer;

use D7_analyzer\Connector\DrushConnector;
use D7_analyzer\DataExtractor\DrushDataExtractor;
use D7_analyzer\Theme\D7TemplateExtractor;
use D7_analyzer\Theme\D7ThemeFileScan;
use D7_analyzer\Modules\D7ModuleAnalysis;
use Symfony\Component\Yaml\Yaml;

class D7CodebaseAnalysis {

  public $globalThemePath;
  public $templateExtractor;
  public $themeFileScanner;
  public $modAnalysis;
  public $config;
  public $drushConnector;
  public $drushDataExtractor;
  public $sitesPath;

  public function __construct() {
    // Set up classes.
    $this->modAnalysis = new D7ModuleAnalysis();
    $this->templateExtractor = new D7TemplateExtractor();
    $this->themeFileScanner = new D7ThemeFileScan();
    $this->drushDataExtractor = new DrushDataExtractor();
    $this->drushConnector = new DrushConnector();
    // Get project config.
    $project_config = file_get_contents('./config.yml');
    $this->config = Yaml::parse($project_config);
    if (isset($this->config['d7_custom_theme'])) {
      $this->globalThemePath = implode('/', [
        $this->config['global_project_path'],
        $this->config['web_root'],
        'sites/all/themes',
        $this->config['d7_custom_theme']
      ]);
    }
    $this->sitesPath =
      $this->config['global_project_path'] .
      "/" .
      $this->config['web_root'] .
      '/sites';
    // Set up drush connection.
    $this->drushConnector->setProjectRoot($this->config['drush_root']);
    $this->drushConnector->setProjectUri($this->config['drush_uri']);
    $this->drushConnector->setContainerName($this->config['container_name']);
  }

  /**
   * Scans for template files in theme.
   *
   * @return array
   */
  public function scanForThemeTemplates() {
    return $this->templateExtractor->extractTemplates($this->globalThemePath, '*.tpl.php');
  }

  /**
   * Scan the theme file.
   *
   * @return false|mixed
   */
  public function scanThemeFile() {
    $theme_file = $this->globalThemePath . '/' . $this->config['d7_custom_theme'] . '.theme';
    return $this->themeFileScanner->performScan($theme_file);
  }

  /**
   * Runs code sniffer.
   *
   * @param $type
   * @return string
   */
  public function runCodeSniffer($format = 'csv') {
    return $this->modAnalysis->runPhpcs($format);
  }

  /**
   * Scans for custom modules.
   *
   * @param $limit
   *   Limit the amount of modules to check.
   *
   * @return array
   *   Custom modules.
   */
  public function scanForCustomModules($limit = FALSE, $update = FALSE) {
    if (!$update) {
      // Just return the file.
      $mod_file = file_get_contents('./reports/custom_modules.yml');
      $yaml_to_arr = Yaml::parse($mod_file);
      return $yaml_to_arr['custom_modules'];
    }
    $module_list = [];
    if ($limit) {
      $module_list = $this->modAnalysis->getCustomModules($limit);
    }
    else {
      $module_list = $this->modAnalysis->getCustomModules();
    }
    // Overwrite custom modules file.
    $mod_list = ['custom_modules' => $module_list];
    $yaml = Yaml::dump($mod_list);
    file_put_contents('./reports/custom_modules.yml', $yaml);
    return $module_list;
  }

  public function getCodeSnifferResultsCsv() {
    return file_get_contents('./reports/phpcs_report.csv');
  }

  public function sniffResultsToArray() {
    $sniff_results = [];
    $f = fopen("./reports/phpcs_report.csv", "r");
    while (($row = fgetcsv($f)) !== FALSE) {
      $sniff_results[] = $row;
    }
    fclose($f);
    $sniff_output = [];
    foreach ($sniff_results as $row_num => $sniff_result_cells) {
      foreach ($sniff_result_cells as $cell) {
        if ($cell) {
          $sniff_output[$row_num][] = htmlspecialchars($cell);
        }
      }
    }
    return $sniff_output;
  }

  /**
   * Gets the drupal version from core:status.
   *
   * @return string
   */
  public function getDrupalVersion() {
    $this->drushConnector->setCommand('status drupal-version --format=list');
    return trim($this->drushConnector->run());
  }

  /**
   * Gets the drush version from core:status.
   *
   * @return string
   */
  public function getDrushVersion() {
    $this->drushConnector->setCommand('status drush-version --format=list');
    return trim($this->drushConnector->run());
  }

  /**
   * Checks if this is a multisite.
   *
   * @return string
   */
  public function isThisMultisite() {
    $default_dirs = [
      'all' => TRUE,
      'default' => TRUE
    ];
    $is_multisite = "No";
    $pattern = $this->sitesPath . "/*";
    $dirs = glob($pattern, GLOB_ONLYDIR);
    foreach ($dirs as $dir) {
      $dir_name = basename($dir);
      if (!isset($default_dirs[$dir_name])) {
        $is_multisite = "Yes";
      }
    }

    return $is_multisite;
  }

  /**
   * Gets the installation profile.
   *
   * @return string
   */
  public function getInstallProfile() {
    $this->drushConnector->setCommand('status install-profile --format=list');
    return trim($this->drushConnector->run());
  }
}
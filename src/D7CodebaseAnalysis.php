<?php

namespace D7_analyzer;

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

  public function __construct() {
    $this->modAnalysis = new D7ModuleAnalysis();
    $this->templateExtractor = new D7TemplateExtractor();
    $this->themeFileScanner = new D7ThemeFileScan();
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

  public function getDrupalVersion() {
    return '7.9.8';
  }

  public function getDrushVersion() {
    return '8.x';
  }

  public function isThisMultisite() {
    return 'N';
  }

  public function getInstallProfile() {
    return 'panopoly';
  }
}
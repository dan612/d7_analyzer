<?php

namespace DanChadwick\Toolshed\Theme;

class D7TemplateExtractor {
  /**
   * Pulls out all matching files recursively.
   *
   * @param $base
   *   The base path.
   * @param $pattern
   *   The pattern to match.
   * @param $flags
   *   Any option flags.
   *
   * @return array|false
   *   Results, or false if none.
   */
  private function globRecursive($base, $pattern, $flags = 0) {
    $flags = $flags & ~GLOB_NOCHECK;
    if (substr($base, -1) !== DIRECTORY_SEPARATOR) {
      $base .= DIRECTORY_SEPARATOR;
    }
    $files = glob($base.$pattern, $flags);
    if (!is_array($files)) {
      $files = [];
    }
    $dirs = glob($base.'*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_MARK);
    if (!is_array($dirs)) {
      return $files;
    }
    foreach ($dirs as $dir) {
      $dirFiles = $this->globRecursive($dir, $pattern, $flags);
      $files = array_merge($files, $dirFiles);
    }
    return $files;
  }

  /**
   * Extracts all templates from the given theme.
   *
   * @param $path_to_theme
   *   Path to the theme.
   *
   * @param $pattern
   *   Pattern to match template files.
   *
   * @return array
   *   The templates.
   */
  public function extractTemplates($path_to_theme, $pattern) {
    $template_files = $this->globRecursive($path_to_theme, $pattern);
    $templates_output = [];
    foreach ($template_files as $index => $template_file) {
      $template_name = basename($template_file);
      switch ($template_name) {
        case str_starts_with($template_name, 'node'):
          $templates_output['node_templates'][] = $template_name;
          break;
        case str_starts_with($template_name, 'page'):
          $templates_output['page_templates'][] = $template_name;
          break;
        case str_starts_with($template_name, 'view'):
          $templates_output['view_templates'][] = $template_name;
          break;
        case str_starts_with($template_name, 'block'):
          $templates_output['block_templates'][] = $template_name;
          break;
        case str_starts_with($template_name, 'html'):
          $templates_output['html_templates'][] = $template_name;
          break;
        case str_starts_with($template_name, 'field'):
          $templates_output['field_templates'][] = $template_name;
          break;
        case str_starts_with($template_name, 'comment'):
          $templates_output['comment_templates'][] = $template_name;
          break;
        case str_starts_with($template_name, 'user'):
          $templates_output['user_templates'][] = $template_name;
          break;
        default:
          $templates_output['misc_templates'][] = $template_name;
          break;
      }
    }
    $counts = [
      'node_templates' => count($templates_output['node_templates']),
      'page_templates' => count($templates_output['page_templates']),
      'view_templates' => count($templates_output['view_templates']),
      'block_templates' => count($templates_output['block_templates']),
      'html_templates' => count($templates_output['html_templates']),
      'field_templates' => count($templates_output['field_templates']),
      'comment_templates' => count($templates_output['field_templates']),
      'user_templates' => count($templates_output['field_templates']),
      'misc_templates' => count($templates_output['field_templates'])
    ];
    // Sort in alphabetical order, matching table to lists.
    ksort($counts);
    $templates_output['counts'] = $counts;
    ksort($templates_output);

    return $templates_output;
  }
}
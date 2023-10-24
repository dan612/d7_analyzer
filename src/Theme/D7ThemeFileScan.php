<?php

namespace D7_analyzer\Theme;

class D7ThemeFileScan {

  public function performScan($path_to_file) {
    if (!file_exists($path_to_file)) {
      return FALSE;
    }
    // Do some scannin'.
    return $path_to_file;
  }

}
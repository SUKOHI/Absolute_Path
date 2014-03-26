<?php

class Absolute_Path {

    private $_top_url;
    private $_current_url;

    public function __construct($base_url='') {

    	if($base_url != '') {
    		
    		$this->setBaseUrl($base_url);
    		
    	}
    	
    }
    
    public function setBaseUrl($url) {
    	
    	$this->_top_url = $this->getTopPath($url);
    	
    	if(strlen($this->_top_url) > strlen($url)) {
    	
    		$base_url = $this->_top_url;
    	
    	}
    	
    	$this->_current_url = $this->getCurrentPath($url);
    	
    }

    public function get($target_path) {

        $first_str = substr($target_path, 0, 1);

        if($first_str == '/') {

            return $this->_top_url . substr($target_path, 1);

        } else if(substr($target_path, 0, 2) == './') {

            return $this->_current_url . substr($target_path, 2);

        } else if(strstr($target_path, '../')) {

            return $this->getOverPath($target_path);

        } else if(substr($target_path, 0, 7) != 'http://' && substr($target_path, 0, 8) != 'https://') {

            return $this->_current_url . $target_path;

        } else {

            return $target_path;

        }

    }

    private function getOverPath($path) {

        $current_url = $this->_current_url;

        $over_count = substr_count($path, '../');

        for($loop = 0; $loop < $over_count; $loop++) {

            $current_url = $this->getParentPath($current_url);

        }

        return $current_url . substr($path, $over_count*3);

    }

    private function getTopPath($path) {

        $return = '';

        $path_parts = explode('/', $path);

        for($loop = 0; $loop < 3; $loop++) {

            $return .= $path_parts[$loop] .'/';

        }

        return $return;

    }

    private function getCurrentPath($path) {

        $return = '';

        $path_parts = explode('/', $path);
        $path_parts_count = count($path_parts);

        for($loop = 0; $loop < $path_parts_count; $loop++) {

            if($loop == $path_parts_count-1) {

                return $return;

            }

            $return .= $path_parts[$loop] .'/';

        }

        return $return;

    }

    private function getParentPath($path) {

        if($path == $this->_top_url) return $path;

        $return = '';

        $path_parts = explode('/', $path);
        $path_parts_count = count($path_parts);

        for($loop = 0; $loop < $path_parts_count; $loop++) {

            if($loop == $path_parts_count-2) {

                return $return;

            }

            $return .= $path_parts[$loop] .'/';

        }

        return $return;

    }

}

/*** Sample Source

    require 'absolute_path.php';

    $base_url = 'http://example.com/test/test/index.html';
    $target_path = '../../test.html';

    $ap = new Absolute_Path();	// or new Absolute_Path($base_url);
    $ap->set($base_url);
    $absolute_path = $ap->get($target_path);

***/

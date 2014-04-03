<?php
	function sx($name){
		global $sxpath;
		$return = '';
		switch($name){
		case 'api_url':
			$return = 'http://moresharesforyou.com/sx/';
		break;
		case 'plugin_url';
			$return = plugins_url()."/".basename($sxpath)."/";
		break;
		default:
			$return =  '';
		break;
		}

		return $return;
	}
?>

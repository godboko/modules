<?php
include_once(GALAXIA_LIBRARY.'/src/API/BaseActivity.php');
//!! Split
//! Split class
/*!
This class handles activities of type 'split'
*/
class Split extends BaseActivity {

	function __construct($db)
	{
        parent::__construct($db);
        $this->type='split';
	}
}
?>
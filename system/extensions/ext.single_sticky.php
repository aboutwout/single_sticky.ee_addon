<?php

/**
* @package ExpressionEngine
* @author Wouter Vervloet
* @copyright  Copyright (c) 2010, Baseworks
* @license    http://creativecommons.org/licenses/by-sa/3.0/
* 
* This work is licensed under the Creative Commons Attribution-Share Alike 3.0 Unported.
* To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/
* or send a letter to Creative Commons, 171 Second Street, Suite 300,
* San Francisco, California, 94105, USA.
* 
*/

if ( ! defined('EXT')) { exit('Invalid file request'); }

class Single_sticky
{
  public $settings            = array();
  
  public $name                = 'Single Sticky';
  public $version             = 0.1;
  public $description         = "Enforce the presence of only one sticky per weblog.";
  public $settings_exist      = 'y';
  public $docs_url            = '';

	// -------------------------------
	// Constructor
	// -------------------------------
	function Single_sticky($settings='')
	{
	  $this->__construct($settings);
	}
	
	function __construct($settings='')
	{	  
		$this->settings = $settings;	
	}
	// END Auto_expire_ext
	
	
  /**
  * Set the expiration date if needed
  */
  function check_entries($weblog_id=0, $autosave=false)
  {
    
    global $IN;
    
    $weblog_id = $IN->GBL('weblog_id', 'POST');
    $is_sticky = $IN->GBL('expiration_date', 'POST');
    
    if(!$weblog_id || $autosave === true || !$is_sticky) return;

  }
  // END set_expiration_date
  
  /**
  * Modifies control panel html by adding the Auto Expire
  * settings panel to Admin > Weblog Administration > Weblog Management > Edit Weblog
  */
  function settings()
  {
    global $IN, $DB, $DSP, $LANG;
    
    $settings['enable']   = array('r', array('yes' => "yes", 'no' => "no"), 'no');
    
    return $settings;
    
  }
  // END settings

  
  
	// --------------------------------
	//  Activate Extension
	// --------------------------------
	function activate_extension()
	{
	  
	  global $DB;

    $sql = array();

    /**
    * @todo
    */

    // hooks array
    $hooks = array(
      'submit_new_entry_start' => 'check_entries',
      'weblog_standalone_insert_entry' => 'check_entries'
    );

    // insert hooks and methods
    foreach ($hooks AS $hook => $method)
    {
      // data to insert
      $data = array(
        'class'		=> get_class($this),
        'method'	=> $method,
        'hook'		=> $hook,
        'priority'	=> 1,
        'version'	=> $this->version,
        'enabled'	=> 'y',
        'settings'	=> ''
      );

      // insert in database
      $sql[] = $DB->insert_string('exp_extensions', $data);
    }

    // add extension table
    $sql[] = 'DROP TABLE IF EXISTS `exp_auto_expire`';
    $sql[] = "CREATE TABLE `exp_auto_expire` (`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, `weblog_id` INT NOT NULL UNIQUE KEY, `time_diff` INT NOT NULL, `time_unit` INT NOT NULL, `status` INT NOT NULL)";

    // run all sql queries
    foreach ($sql as $query) {
      $DB->query($query);
    }

    return true;
	}
	// END activate_extension
	 
	 
	// --------------------------------
	//  Update Extension
	// --------------------------------  
	function update_extension($current='')
	{
	  global $DB;
		
    if ($current == '' OR $current == $this->version)
    {
      return FALSE;
    }
    
    if($current < $this->version) { }

    // init data array
    $data = array();

    // Add version to data array
    $data['version'] = $this->version;    

    // Update records using data array
    $sql = $DB->update_string('exp_extensions', $data, "class = '".get_class($this)."'");
    $DB->query($sql);
  }
  // END update_extension

	// --------------------------------
	//  Disable Extension
	// --------------------------------
	function disable_extension()
	{	
	  global $DB;
	
    // Delete records
    $DB->query("DELETE FROM exp_extensions WHERE class = '".get_class($this)."'");
  }
  // END disable_extension

	 
}
// END CLASS
?>
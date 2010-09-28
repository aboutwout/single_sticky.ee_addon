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
    
    global $IN, $DB, $SESS;
    
    $weblog_id = $IN->GBL('weblog_id', 'POST');
    $is_sticky = ( $IN->GBL('sticky', 'POST') == 'y');
    
    $entry_id = $IN->GBL('entry_id', 'POST');
    $author = $SESS->userdata['member_id'];
    
    if(!$weblog_id || $autosave === true || !$is_sticky) return;
        
    $DB->query($DB->update_string('exp_weblog_titles', array('sticky' => 'n'), "weblog_id='$weblog_id' AND sticky='y' AND entry_id <> '$entry_id'"));
    
  }
  // END check_entries
  
  /**
  * Modifies control panel html by adding the Auto Expire
  * settings panel to Admin > Weblog Administration > Weblog Management > Edit Weblog
  */
  function settings_form($settings=array())
  {
    global $DSP, $LANG, $IN, $DB;
      
    $DSP->crumbline = TRUE;
    
    $DSP->title  = $LANG->line('single_sticky_extension_name');
    $DSP->crumb  = $DSP->anchor(BASE.AMP.'C=admin'.AMP.'area=utilities', $LANG->line('utilities')).
    $DSP->crumb_item($DSP->anchor(BASE.AMP.'C=admin'.AMP.'M=utilities'.AMP.'P=extensions_manager', $LANG->line('extensions_manager')));
    $DSP->crumb .= $DSP->crumb_item($LANG->line('single_sticky_extension_name'));
  
    $DSP->right_crumb($LANG->line('disable_extension'), BASE.AMP.'C=admin'.AMP.'M=utilities'.AMP.'P=toggle_extension_confirm'.AMP.'which=disable'.AMP.'name=single_sticky');
		$DSP->body .= $DSP->heading($LANG->line('single_sticky_extension_name'));

    $weblog_query = $DB->query("SELECT weblog_id, blog_title FROM exp_weblogs");

    $weblogs = array();
      
    foreach($weblog_query->result as $row) {          
  
      $weblogs[] = array(
        'id' => $row['weblog_id'],
        'title' => $row['blog_title'],
        'enabled' => ( isset($settings[$row['weblog_id']]) ) ? $settings[$row['weblog_id']] : 'n'
      );
    }
    
    $vars = array(
      'weblogs' => $weblogs,
      'settings_saved' => $_SERVER['REQUEST_METHOD']=='POST'
    );
    
    $DSP->body .= $DSP->view(PATH_EXT.'single_sticky/views/settings_form.php', $vars, TRUE);

  }
  // END settings

  /**
  * Save settings
  */
  function save_settings()
  {
    global $DB;
    
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ss_enabled']) )
    {
      $this->settings = $_POST['ss_enabled'];
      $DB->query($DB->update_string('exp_extensions', array('settings' => serialize($this->settings)), 'class = "'.get_class($this).'"'));    
    }
    
  }

  
  
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
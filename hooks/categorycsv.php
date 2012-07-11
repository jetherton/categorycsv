<?php defined('SYSPATH') or die('No direct script access.');
/**
 * categorycsv Hook - Load All Events
 *
 
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Category CSV Ushahidi Plugin - https://github.com/jetherton/categorycsv
  
 */

class categorycsv {
	
	/**
	 * Registers the main event add method
	 */
	public function __construct()
	{
	
		// Hook into routing

		Event::add('system.pre_controller', array($this, 'add'));

	}
	
	/**
	 * Adds all the events to the main Ushahidi application
	 */
	public function add()
	{
		Event::add('ushahidi_action.nav_admin_reports', array($this, '_set_menu')); 
		
	}
	
	
	public function _set_menu()
	{
		$this_sub_page = Event::$data;
		
		echo ($this_sub_page == "upload") ? Kohana::lang('categorycsv.menu') : "<a href=\"".url::base()."admin/categorycsv/reports/download\">".Kohana::lang('categorycsv.menu')."</a>";
		
	}
	
}//end class

new categorycsv;
<?php defined('SYSPATH') or die('No direct script access.');
/**
 * categorycsv Controller - Creates the download reports page
 *
 
 * @author	   John Etherton <john@ethertontech.com> 
 * @package	   Category CSV Ushahidi Plugin - https://github.com/jetherton/categorycsv
  
 */

class Reports_Controller extends Admin_Controller
{
    function __construct()
    {
        parent::__construct();

        $this->template->this_page = 'reports';
        $this->params = array('all_reports' => TRUE);
        
    }


    function download()
    {
    	
    	
    	
    	// If user doesn't have access, redirect to dashboard
    	if ( ! admin::permissions($this->user, "reports_download"))
    	{
    		url::redirect(url::site().'admin/dashboard');
    	}

    	
        $cat_array = array();

        $this->template->content = new View('categorycsv/reports_download');
        $this->template->content->title = Kohana::lang('ui_admin.download_reports');

        $form = array(
            'data_point'   => '',
            'data_include' => '',
            'from_date'    => '',
            'to_date'      => ''
        );
        
        $errors = $form;
        $form_error = FALSE;

        // check, has the form been submitted, if so, setup validation
        if ($_POST)
        {
            // Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
            $post = Validation::factory($_POST);

             //  Add some filters
            $post->pre_filter('trim', TRUE);

            // Add some rules, the input field, followed by a list of checks, carried out in order
            $post->add_rules('data_point.*','required','numeric','between[1,4]');
            $post->add_rules('data_include.*','numeric','between[1,6]');
            $post->add_rules('from_date','date_mmddyyyy');
            $post->add_rules('to_date','date_mmddyyyy');
            
       

            // Validate the report dates, if included in report filter
            if (!empty($_POST['from_date']) || !empty($_POST['to_date']))
            {
                // Valid FROM Date?
                if (empty($_POST['from_date']) || (strtotime($_POST['from_date']) > strtotime("today"))) {
                    $post->add_error('from_date','range');
                }

                // Valid TO date?
                if (empty($_POST['to_date']) || (strtotime($_POST['to_date']) > strtotime("today"))) {
                    $post->add_error('to_date','range');
                }

                // TO Date not greater than FROM Date?
                if (strtotime($_POST['from_date']) > strtotime($_POST['to_date'])) {
                    $post->add_error('to_date','range_greater');
                }
            }


            // Test to see if things passed the rule checks
            if ($post->validate())
            {
            	
            	
            	
            	
                // Add Filters
                $filter = " ( ";
                
                // Report Type Filter
                $show_active = false;
                $show_inactive = false;
                $show_verified = false;
                $show_not_verified = false;
                
                // Report Type Filter
                foreach($post->data_point as $item)
                {
                    if ($item == 1) {
                        $show_active = true;
                    }
                    if ($item == 2) {
                        $show_verified = true;
                    }
                    if ($item == 3) {
                        $show_inactive = true;
                    }
                    if ($item == 4) {
                        $show_not_verified = true;
                    }
                }
                
                
                // Handle active or not active
                if ($show_active && !$show_inactive)
                {
                	$filter .= ' incident_active = 1 ';
                }
                elseif (!$show_active && $show_inactive)
                {
                	$filter .= '  incident_active = 0 ';
                }
                elseif ($show_active && $show_inactive)
                {
                	$filter .= ' (incident_active = 1 OR incident_active = 0) ';
                }
                
                // Neither active nor inactive selected: select nothing
                elseif (!$show_active && !$show_inactive)
                {
                	// Equivalent to 1 = 0
                	$filter .= ' (incident_active = 0 AND incident_active = 1) ';
                }
                
                $filter .= ' AND ';
                
                // Handle verified
                if($show_verified && !$show_not_verified)
                {
                	$filter .= ' incident_verified = 1 ';
                }
                elseif (!$show_verified && $show_not_verified)
                {
                	$filter .= ' incident_verified = 0 ';
                }
                elseif ($show_verified && $show_not_verified)
                {
                	$filter .= ' (incident_verified = 0 OR incident_verified = 1) ';
                }
                elseif (!$show_verified && !$show_not_verified)
                {
                	$filter .= ' (incident_verified = 0 AND incident_verified = 1) ';
                }
                
                
                $filter .= ") ";
		
		//are we stripping out HTML from the description
		$strip_html = false;
		if(isset($post->strip_html))
		{
			$strip_html = true;
		}

         // Report Date Filter
		if (!empty($post->from_date) && !empty($post->to_date))
		{
			$filter .= " AND ( incident_date >= '" . date("Y-m-d H:i:s",strtotime($post->from_date)) . "' AND incident_date <= '" . date("Y-m-d H:i:s",strtotime($post->to_date)) . "' ) ";
		}

		        // Retrieve reports
         $incidents = ORM::factory('incident')
			->where($filter)
			->orderby('incident_dateadd', 'desc')
			->find_all();
         
         


                // Column Titles
                echo "#,INCIDENT TITLE,INCIDENT DATE";
                foreach($post->data_include as $item)
                {
                	
                    if ($item == 1) {
                        echo ",LOCATION";
                    }
                    
                    if ($item == 2) {
                        echo ",DESCRIPTION";
                    }
                    
                    if ($item == 3) {
                    	
                        $cats = ORM::factory('category')->where('category_visible','1')->find_all();
                        foreach($cats as $cat)
                        {
                        	echo ',"'.$this->_csv_text($cat->category_title).'"';
                        	
                        	$cat_array[$cat->id] = $cat->category_title;
                        }
                    }
                    
                    if ($item == 4) {
                        echo ",LATITUDE";
                    }
                    
                    if($item == 5) {
                        echo ",LONGITUDE";
                    }		   
                }
                echo ",APPROVED,VERIFIED";
                echo "\n";
                

                foreach ($incidents as $incident)
                {
                    echo '"'.$incident->id.'",';
                    echo '"'.$this->_csv_text($incident->incident_title).'",';
                    echo '"'.$incident->incident_date.'"';

                    foreach($post->data_include as $item)
                    {
                        switch ($item)
                        {
                            case 1:
                                echo ',"'.$this->_csv_text($incident->location->location_name).'"';
                            break;

                            case 2:
								if($strip_html)
								{
									echo ',"'.$this->_csv_text(strip_tags($incident->incident_description)).'"';
								}
								else
								{
									echo ',"'.$this->_csv_text($incident->incident_description).'"';
								}
                            break;

                            case 3:
								//first do the site wide categories
								$temp_cat_array = array();
								
								//put the incident cats in a temp array so we don't keep hitting the DB
								foreach($incident->incident_category as $category)
								{
									$temp_cat_array[] = $category->category->id;
								}
								
								
                                foreach($cat_array as $cat_id=>$cat_title)
                                {
                                	if(in_array($cat_id, $temp_cat_array))
                                	{
                                		//echo ',"'.$this->_csv_text($cat_title).'"';
                                		echo ',"'.Kohana::lang('categorycsv.yes').'"';
                                	}
                                	else
                                	{
                                		echo ',""';
                                	}
                                	
                                }
                                                                
				
                            break;
                        
                            case 4:
                                echo ',"'.$this->_csv_text($incident->location->latitude).'"';
                            break;
                        
                            case 5:
                                echo ',"'.$this->_csv_text($incident->location->longitude).'"';
                            break;
                        }
                    }
                    
                    if ($incident->incident_active)
                    {
                        echo ",YES";
                    }
                    else
                    {
                        echo ",NO";
                    }
                    
                    if ($incident->incident_verified)
                    {
                        echo ",YES";
                    }
                    else
                    {
                        echo ",NO";
                    }
                    
                    echo "\n";
                    unset($incident);
                }
                


                // Output to browser
                header("Content-type: text/x-csv");
                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header("Content-Disposition: attachment; filename=" . time() . ".csv");
                //header("Content-Length: " . strlen($report_csv));
                //echo $report_csv;
                exit;

            }
            // No! We have validation errors, we need to show the form again, with the errors
            else
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('report'));
                $form_error = TRUE;
            }
        }

        $this->template->content->form = $form;
        $this->template->content->errors = $errors;
        $this->template->content->form_error = $form_error;

        // Javascript Header
        $this->template->js = new View('simplegroups/reports_download_js');
        $this->template->js->calendar_img = url::base() . "media/img/icon-calendar.gif";
    }//end method
    
    
    
    private function _csv_text($text)
    {
    	$text = stripslashes(htmlspecialchars($text));
    	return $text;
    }

    
}//end class

<?php

class Gravity_Forms_Notification_Viewer {

	private $forms = array();
	private $notices = array();
	private $emails = array();

    //////
    // GETTERS
    //
    function get_forms() {
    	return $this->forms;
    }
    
    function get_form($id) {
    	return $this->forms[$id];
    }
    
    function get_notices() {
    	return $this->notices;
    }
    
    function get_notice($form_id, $notice_id) {
		$hybrid_id = $this->hybrid_id($form_id, $notice_id);
    	return $this->notices[$hybrid_id];
    }
    
    function get_emails() {
    	// Sorting out for the user.
    	// I don't expect get_emails() to be called
    	//   more than once per page load.
    	ksort ($this->emails);
    	return $this->emails;
    }
    
    function get_email($email) {
    	return $this->emails[$email];
    }
    
    
    
	function __construct() {
		
		$raw_data = $this->get_raw_form_data();
		
    	foreach ($raw_data as $form) {
			/*
			print '<pre>';
			print_r($form);
			print '</pre>';
			*/
			
			// Don't list "trashed" forms.
			if ($form['is_trash']) {
				continue;
			}
			
    		// We'll need this later.
    		$this->forms[$form['id']] = array(
    			'title' => $form['title'],
    			'active' => $form['is_active'],
    		);

			// Remember all the notice ID for a form.
        	$notice_keys = array();
        	
        	$notifications = json_decode($form['notifications']);
        	foreach ($notifications as $notice) {
				$this->process_notification($notice, $form['id']);
				$notice_keys[] = $notice->id;
			}
			
			$this->forms[$form['id']]['notices'] = $notice_keys;
        	$this->forms[$form['id']]['notice_count'] = count($notice_keys);
    	}
	}

	function process_notification($notice, $form_id) {
		/*
		print '<pre>';
		print_r($notice);
		print '</pre>';
		*/
		
		// A notitfication which doesn't have an 
		//   "isActive" value _IS_, in fact, active.
		$isActive = (!isset($notice->isActive) || $notice->isActive);
    	
		//////
		// A hybrid form-notice ID is needed because when a
		//   Gravity Form is cloned, the notification ID is
		//   also cloned.
		$hybrid_id = $this->hybrid_id($form_id, $notice->id);
		
		$this->notices[$hybrid_id] = array(
			'title' => $notice->name,
			'active' => $isActive,
		);

		$this->gnfv_parse_email_list($notice->to, $hybrid_id, 'TO');
		$this->gnfv_parse_email_list($notice->bcc, $hybrid_id, 'BCC');
	}
	

    function gnfv_parse_email_list($string_csv, $notice_id, $record_as) {
    
    	$array = split(',',$string_csv);
		foreach ($array as $email) {

			// The comma-delimiated list may include spaces.
			$email = trim($email);

			// Skip if blank.
        	if (!$email) { continue; }
        	
			$this->gfnv_store_email($email, $notice_id, $record_as);
		}
    	
    }
    
    function gfnv_store_email($email, $notice_id, $record_as) {
    
    	// Build an array to store email modes for a particular email.
    	if (!isset($this->emails[$email])) {
    		$this->emails[$email] = array();
    	}
    	
    	// Append the new mode.
    	$v2 = '';
    	if (isset($this->emails[$email][$notice_id])) {
    		$v2 = $this->emails[$email][$notice_id];
    		$v2 .= '/';
    	}
    	$v2 .= $record_as;
    	
	
		// Save changes.
		$this->emails[$email][$notice_id] = $v2;
    }
   
	// TODO: perhaps we can ask Gravity Forms for this data?
    function get_raw_form_data() {
        global $wpdb;
        $query = '
            SELECT *
            FROM '.$wpdb->base_prefix.'rg_form AS rg_form
            LEFT JOIN '.$wpdb->base_prefix.'rg_form_meta AS rg_form_meta
            ON rg_form.id = rg_form_meta.form_id
            ORDER BY rg_form.id
        ';
        return $wpdb->get_results($query, ARRAY_A);
    }
    
    // Forces us to stay consistent.
    function hybrid_id($form_id, $notice_id) {
    	return $form_id . '-' . $notice_id;
    }
}
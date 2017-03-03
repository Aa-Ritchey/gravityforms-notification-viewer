<?php
	// Only create the object when we display this page.
	$gfnv_object = new Gravity_Forms_Notification_Viewer();
?>

<!-- TODO: migrate this CSS into a separate file(?) -->
<style>
	ul.gfnv_list {
		list-style: initial;
		padding-left: 1em;
	}
	
	#gfnv_table {
		/* reset */
		width: auto;
		
		/* kludge, this fixes a design bug caused by border-left below */
		border-left: 0;
	}

	#gfnv_table thead .leftmost-heading-col {
		text-align: right;
	}
	
	#gfnv_table thead th {
		text-align: center;
		border-left: 1px solid #e5e5e5;
	}
	
	/* TODO: move to the proper place. */
	#gfnv_table td {
		text-align: center;
		border-left: 1px solid #e5e5e5;
	}
	
	#gfnv_table .email {
		text-align: right;
	}
	
	#gfnv_table .inactive {
		/* we don't use opacity because it will also affect border-left */
		color: #dddddd;
	}
	
	#gfnv_table .inactive:hover {
		/* improve usability */
		/* probably not the exact color; close enough */
		color: inherit;
	}
	#gfnv_table .inactive a {
		opacity: 0.2;
	}
	
	#gfnv_table .inactive:hover a {
		/* improve usability */
		/* (meant to activate when hovering over cell) */
		opacity: 1;
	}
</style>



<div class="wrap">
	<h1>Gravity Forms Notification Viewer &beta;</h1>
    <p>See who is notified when a form is submitted.</p>

<?php
    print '<!-- Not using WP_List_Table because we simply want a simple list -->';
    print '<table id="gfnv_table" class="widefat striped">';
    
    
    
   	///////////////
	// FIRST ROW //

	print '<thead>';
    print '<tr>';
    print '<th class="leftmost-heading-col">Form &rarr;</th>';
    
    // Link to each form's notification page.
    //   (i.e. page showing all notifications made by a form.)
    foreach ($gfnv_object->get_forms() as $form_id => $form) {
    
    	$class = $form['active'] ? '' : 'inactive';
    	
    	$colspan = $form['notice_count'];
    	
		$href = get_site_url();
		$href .= '/wp-admin/admin.php?page=gf_edit_forms&view=settings&subview=notification';
		$href .= '&id='.$form_id;
		
		// TODO: sanitize $title
		
		$title = $form['title'];
		
    	printf('<th class="%s" colspan="%d"><a href="%s" title="%s">%s</a></th>', $class, $colspan, $href, $title, $form_id);
    }
    print '</tr>';



   	////////////////
	// SECOND ROW //

    print '<tr>';
    print '<th class="leftmost-heading-col">Notification &rarr;</th>';
    
    // Link to each notification's page.
    //   (i.e. page where you can edit email addresses.)
    foreach ($gfnv_object->get_forms() as $form_id => $form) {
    	
    	foreach ($form['notices'] as $notice_id) {
		
			$notice = $gfnv_object->get_notice($form_id, $notice_id);
			
			$class = ($form['active'] && $notice['active']) ? '' : 'inactive';
			
			$href = get_site_url();
			$href .= '/wp-admin/admin.php?page=gf_edit_forms&view=settings&subview=notification';
			$href .= '&id='.$form_id;
			$href .= '&nid='.$notice_id;
			
			$title = $notice['title'];
			
			printf('<th class="%s"><a href="%s" title="%s">EDIT</a></th>', $class, $href, $title);
    	}
    }
    
    print '</tr>';
	print '</thead>';

   	////////////////////
	// REMAINING ROWS //

	print '<tbody>';
    foreach ($gfnv_object->get_emails() as $email => $email_data) {
    	print '<tr>';
    	printf('<td class="email">%s</td>', $email);
    	
		$forms = $gfnv_object->get_forms();
			
		foreach ($forms as $form_id => $form_data) {
			
			foreach ($form_data['notices'] as $notice_id) {
				
				$notice = $gfnv_object->get_notice($form_id, $notice_id);
				
				$class = ($form_data['active'] && $notice['active']) ? '' : 'inactive';
    			
    			$hybrid_id = $gfnv_object->hybrid_id($form_id, $notice_id);
    			
    			$status = isset($email_data[$hybrid_id]) ? $email_data[$hybrid_id] : '';
    			
				printf('<td class="%s">%s</td>', $class, $status);
			}
		}
		
    	print '</tr>';
    }
	print '</tbody>';
    
    print '</table>';
    
?>

	<h2>Tips</h2>
	<ul class="gfnv_list">
		<li>Faded text means the notification will not get sent because either the form or the notification is inactive.</li>
		<li>Hover on a form ID hyperlink to see the form's name.</li>
		<li>Hover on a form notification hyperlink to see the notification's name.</li>
		<li>The email <strong>(FORM-FIELD)</strong> is unknown, and can vary based on what the user submitted.</li>
		<li>The email <strong>(ADMIN)</strong> is whatever <tt>{admin_email}</tt> is set to for this WordPress install.</li>
	</ul>
</div>
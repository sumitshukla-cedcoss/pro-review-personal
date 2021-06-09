<?php 
	$total         = get_comments();
	$wp_list_table = _get_list_table( 'WP_Comments_List_Table' );
	$wp_list_table->display( true );

<?php 

function branch_page_handler() {
	global $wpdb;

    $table = new Branch_List();
    $table->prepare_items();
	
	$page['new'] = TRUE;
	$page['page'] = 'branch_add';

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );
	
	?>

	<form method="get" class="posts-filter">
		<input type="hidden" name="page" value="branch"/>

		<?php $table->search_box( __('Cari','alifakids'), null ) ?>
		<?php
			$table->display(); 
		?>
	</form>


	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}
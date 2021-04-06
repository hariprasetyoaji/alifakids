<?php 

function teachers_page_handler() {
	global $wpdb;

   	$table = new Teachers_List();
   	$table->prepare_items();

	$page['new'] = TRUE;
	$page['page'] = 'teacher_add';

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );
	
	?>

	<form method="get" class="posts-filter">
		<input type="hidden" name="page" value="teacher"/>

		<?php $table->search_box( __('Cari','alifakids'), null ) ?>
		<?php $table->display(); ?>

	</form>


	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}
<?php 

function students_page_handler() {
	global $wpdb;

    $table = new Students_List();
    $table->prepare_items();
	
	$page['new'] = TRUE;
	$page['page'] = 'students_add';

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );
	
	?>

	<ul class="subsubsub">
		<li class="all">
			<a href="users.php" class="current" aria-current="page">
				All 
				<span class="count">(<?php echo $table->record_count() ?>)</span>
			</a> |
		</li>
		<li class="administrator">
			<a href="users.php?role=administrator">
				Administrator 
				<span class="count">(<?php echo $table->record_count('1') ?>)</span>
			</a>
		</li>
	</ul>
	<form method="get" class="posts-filter">
		<input type="hidden" name="page" value="students"/>

		<?php $table->search_box( __('Cari','alifakids'), null ) ?>
		<?php
			$table->display(); 
		?>
	</form>


	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}
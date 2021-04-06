<div class="wrap">
	<h1 class="wp-heading-inline">

		<?php if ( isset( $page['page'] ) && ( $page['page'] == 'report_daily_add' ) && isset( $page['edit'] ) ): ?>
			<?php echo __( 'Tambah Report Harian', 'alifakids' ) ?>
		<?php elseif ( isset( $page['page'] ) && ( $page['page'] == 'payment_add' ) && isset( $page['edit'] ) ): ?>
			<?php echo __( 'Detail Pembayaran', 'alifakids' ) ?>
		<?php else: ?>
	    	<?php echo get_admin_page_title() ?>
		<?php endif ?>

	</h1>
	<?php if ( (isset($page['new']) && $page['new']) || 
		( (isset($page['edit']) && $page['edit']) && (isset($_REQUEST['id'])) ) 
		&& ($page['page'] != 'report_daily_add' || $page['page'] != 'report_weekly_add' || $page['page'] != 'payment_add') ) : ?>

	    <a href="<?php echo get_admin_url(); ?>admin.php?page=<?php echo $page['page'] ?>" class="page-title-action">Add New</a>
	<?php endif ?>
	<hr class="wp-header-end">
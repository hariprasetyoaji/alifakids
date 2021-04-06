<?php 

function course_report_page_handler() {
	global $wpdb;

	$page['edit'] = FALSE;
	$page['page'] = 'course-report';

	$table_name = $wpdb->prefix . 'course_report'; 

	$item = [];
 	$class = (isset($_REQUEST['class'])) ? $_REQUEST['class'] : '';
	$lesson = (isset($_REQUEST['lesson'])) ? $_REQUEST['lesson'] : '';
	$year = (isset($_REQUEST['year'])) ? $_REQUEST['year'] : '';
	$month = (isset($_REQUEST['month'])) ? $_REQUEST['month'] : '';
	$week = (isset($_REQUEST['week'])) ? $_REQUEST['week'] : '';
	$day = (isset($_REQUEST['day'])) ? $_REQUEST['day'] : '';

    if ( $_REQUEST['class'] &&
		 $_REQUEST['year'] &&
		 $_REQUEST['month'] &&
		 $_REQUEST['week'] &&
		 $_REQUEST['day'] ) {
        $item = $wpdb->get_results(
        	$wpdb->prepare( " 
            		SELECT cr.*,
            				s.*
            		FROM $table_name cr 
            			LEFT JOIN {$wpdb->prefix}students s
            				ON cr.student_id = s.student_id
            		WHERE cr.year = %d 
            			AND cr.month = %d 
            			AND cr.week = %d 
            			AND cr.day = %d 
            			AND s.class_id = %d 
    			", 
        		$year,$month,$week,$day,$class
        	), 
        	ARRAY_A
        );


        if (!$item) {
            $item = [];
            $notice = __('Item not found', 'alifakids');
        }
    } else {
    	$_SESSION['notice'] = 'Semua filter harus di isi untuk membuka laporan.';
    	$redirect_args = array(
    		'class' => $class, 
    		'lesson' => $lesson, 
    		'year' => $year, 
    		'month' => $month, 
    		'week' => $week, 
    		'day' => $day, 
    	);
		wp_redirect( add_query_arg( $redirect_args,  admin_url('edit.php?post_type=course') ));
		exit();
    }

	do_action( 'add_meta_boxes' );

	//$taxonomies = wp_get_post_terms($_REQUEST['id'], 'lesson')[0];

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );

	?>
	<form  method="get" action="">
		<h1 class="wp-heading-inline">
			Laporan Pembelajaran 
			<?php echo getClassName($class); ?>
			<?php echo $year ?> - 
			<?php echo 'Bulan '.$month ?> - 
			<?php echo 'Minggu '.$week ?> - 
			<?php echo 'Day '.$day ?> - 
		</h1>
		<a href="<?php echo admin_url('edit.php?post_type=course'); ?>" class="page-title-action">Kembali ke Daftar Laporan</a>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content" >
	                <?php do_meta_boxes('admin_page_course-report', 'normal', $item ); ?>
	            </div>
	            <div id="postbox-container-1" class="postbox-container">
	                <?php do_meta_boxes('admin_page_course-report_export', 'normal', $item); ?>
	            </div>
			</div>
		</div>
	</form>

	<script type="text/javascript">
		jQuery(document).ready(function($) {
			jQuery('.course-report-title-wrapper').click(function(event) {
				var itemDiv = jQuery(this).parent().closest('.course-report-item');

				itemDiv.find('.course-report-box').toggle(400);
			});
		});
	</script>

	<?php
}

add_action('add_meta_boxes','alifakids_add_my_meta_box', 10, 2);
function alifakids_add_my_meta_box() {
    add_meta_box(
        'course_report_meta',
        __('Laporan Pembelajaran','alifakids'), 
        'course_report_meta_callback',
        'admin_page_course-report',
        'normal',
        'default' 
	);

	 add_meta_box(
        'course_report_meta_export',
        __('Export Laporan','alifakids'), 
        'course_report_export_callback',
        'admin_page_course-report_export',
        'normal',
        'default' 
	);
}

function course_report_meta_callback($item) {
	?>
	<?php if ($item): ?>
	<div class="course-report-meta-wrapper">
		<?php foreach ($item as $key => $value): ?>

			<div class="course-report-item">
				<div class="course-report-title-wrapper">
					<h3>
						<?php echo $value['name'] ?> -
						<span class="text-right">
							<?php echo date_i18n("l, d F Y H:i:s", strtotime( $value['date'] ) ); ?>
						</span>
					</h3>
					
				</div>
				<div class="course-report-box">
					<div class="course-report-option-wrapper">
			            <label>Foto</label>
                        <div class="course-report-option-field"><?php echo wp_get_attachment_image( $value['attachment'],'medium'); ?></div>
			        </div>
			        <div class="course-report-option-wrapper">
			            <label>1. Pada bagian sesi mana Ananda menunjukkan antusias?</label>
                        <div class="course-report-option-field"><?php echo $value['point_1'] ?></div>
			        </div>
			        <div class="course-report-option-wrapper">
			            <label>2. Apa hal yang sudah berjalan baik pada sesi kali ini?</label>
                        <div class="course-report-option-field"><?php echo $value['point_2'] ?></div>
			        </div>
			        <div class="course-report-option-wrapper">
			            <label>3. Apa aksi perbaikan untuk membersamai Ananda di sesi berikutnya?</label>
                        <div class="course-report-option-field"><?php echo $value['point_3'] ?></div>
			        </div>
			        <div class="course-report-option-wrapper">
			            <label>4. Bagian mana yang sangat dikuasai Ananda?</label>
                        <div class="course-report-option-field"><?php echo $value['point_4'] ?></div>
			        </div>
			        <div class="course-report-option-wrapper">
			            <label>5. Bagian mana yang menantang atau silit bagi Ananda?</label>
                        <div class="course-report-option-field"><?php echo $value['point_5'] ?></div>
			        </div>
				</div>
			</div>
			
		<?php endforeach ?>
	</div>
	<?php else: ?>
		<p>Belum ada laporan yang dikirim.</p>
	<?php endif ?>
<?php
}

function course_report_export_callback($item) {
	?>
	<?php if ($item): 
		$class = (isset($_REQUEST['class'])) ? $_REQUEST['class'] : '';
		$year = (isset($_REQUEST['year'])) ? $_REQUEST['year'] : '';
		$month = (isset($_REQUEST['month'])) ? $_REQUEST['month'] : '';
		$week = (isset($_REQUEST['week'])) ? $_REQUEST['week'] : '';
		$day = (isset($_REQUEST['day'])) ? $_REQUEST['day'] : '';

		$export_args = array(
			'action' => 'export_excel_course_report',
			'class' => $class, 
    		'year' => $year, 
    		'month' => $month, 
    		'week' => $week, 
    		'day' => $day, 
		);
	?>
	<a style="width: 100%; text-align:center; display: block; margin-top: 10px;"
	 class="button-primary" href="<?php echo add_query_arg( $export_args, admin_url('admin-ajax.php')) ?>" >
		Export to Excel
	</a>
	<?php endif ?>
	<?php

}
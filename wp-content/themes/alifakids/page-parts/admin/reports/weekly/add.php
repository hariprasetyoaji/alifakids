<?php 

function report_weekly_new_page_handler() {
	global $wpdb;

	$page['edit'] = TRUE;
	$page['page'] = 'report_daily_add';

	$table_name = $wpdb->prefix . 'reports_daily'; 

	$student = getStudentByID($_REQUEST['student_id']);

	$reportPoints = getWeeklyReportPoints();

	$default = array(
	    'report_id' => '',
	    'student_id' => $_REQUEST['student_id'],
	    'points' =>  array(),
	    'date' => date('Y-m-d', strtotime('monday this week', strtotime(current_time('Y-m-d'))))
	);
	$item = $default;

    if (isset($_REQUEST['id'])) {
	    $item = $wpdb->get_row(
	      $wpdb->prepare( " 
	          SELECT  
	              r.report_id as report_id,
	              r.student_id as student_id,
	              r.date as date

	          FROM {$wpdb->prefix}students s 
	            LEFT JOIN {$wpdb->prefix}reports_weekly r 
	              ON s.student_id = r.student_id
	          WHERE r.report_id = '%d'
	          ", 
	        $_REQUEST['id'] 
	      ), 
	      ARRAY_A
	    );

	  $item['points'] = getWeeklyReportPointsByID($_REQUEST['id']);

	  $item = shortcode_atts($default, $item);

	  if (!$item) {
	      $notice = __('Item not found', 'alifakids');
	  }
	}

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );
	?>

	 <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" id="dailyReport">
		 <input name='action' type="hidden" value='new_report_weekly'>
          <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('new_report_weekly_admin')?>"/>
          <input type="hidden" name="report_id" value="<?php echo $item['report_id'] ?>"/>
          <input type="hidden" name="student_id" value="<?php echo $item['student_id'] ?>"/>
          <input type="hidden" name="date" value="<?php echo $item['date'] ?>"/>

		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row">
						<label for="name">
							Nama Siswa
						</label>
					</th>
					<td>
						<p><?php echo $student->name ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="branch">
							Cabang
						</label>
					</th>
					<td>
						<p><?php echo getBranchName($student->branch_id) ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="class">
							Kelas
						</label>
					</th>
					<td>
						<p><?php echo getClassName($student->class_id) ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="class">
							Tanggal Report
						</label>
					</th>
					<td>
						<p><?php echo 'Minggu ke-'.weekOfMonth($_REQUEST['date']).', '.date_i18n("F Y", strtotime( $_REQUEST['date'] )) ?></p>
					</td>
				</tr>
			</tbody>	
		</table>

		<h2 class="title">Penilaian</h2>
		<table class="form-table" role="presentation">
			<tbody>

				<?php 
		          $i = 1;
		          $j = 1;
		          foreach ($reportPoints as $points): 
          		?>
				 	<?php if ($points['type'] == 1): ?>
					<tr class="form-required">
						<td>
							<fieldset>
								<legend><strong><?php echo $i.'. '.$points['name'] ?></strong></legend>
								<label>
									<input class="custom-control-input" type="radio" name="<?php echo 'points_'.$points['points_id'] ?>" id="points_<?php echo $points['points_id'].'_1' ?>" value="A" <?php echo (!empty($item['points']) && $item['points'][ $points['points_id'] ] == 'A' ) ? 'checked' : '' ; ?> required>
									A. Baik
								</label>
								<br>
								<label>
									<input class="custom-control-input" type="radio" name="<?php echo 'points_'.$points['points_id'] ?>" id="points_<?php echo $points['points_id'].'_2' ?>" value="B" <?php echo (!empty($item['points']) && $item['points'][ $points['points_id'] ] == 'B' ) ? 'checked' : '' ; ?> required>
									B. Cukup Baik
								</label>
								<br>
								<label>
									 <input class="custom-control-input" type="radio" name="<?php echo 'points_'.$points['points_id'] ?>" id="points_<?php echo $points['points_id'].'_3' ?>" value="C" <?php echo (!empty($item['points']) && $item['points'][ $points['points_id'] ] == 'C' ) ? 'checked' : '' ; ?> required>
									 C. Belum Baik
								</label>
							</fieldset>
						</td>
					</tr>
					<?php $i++; else: ?>

                	<?php if ($j == 1): ?>
                		<tr>
                			<td>
                		 		<p class="m-t-sm"><em>Narasikan minimal 3 kalimat yang menjelaskan perkembangan Ananda dalam menguasai Mata pelajaran selama 1 minggu ini. </em></p>
                			</td>
                		</tr>
                	<?php endif ?>	
                		<tr class="form-required">
                			<td>
                				<legend><strong><?php echo $j.'. '.$points['name'] ?></strong></legend>
                				<textarea rows="3" name="<?php echo 'points_'.$points['points_id'] ?>" class="large-text" required><?php echo (!empty( $item['points'] )) ? $item['points'][ $points['points_id'] ] : ''; ?></textarea>
                			</td>
                		</tr>
					<?php $j++; endif; ?>

	          <?php 
	          	endforeach;
	          ?>
			</tbody>
		</table>
		<p class="submit">
			<input 
				value="<?php _e('Simpan', 'alifakids')?>"
				type="submit" 
				name="submit" 
				class="button button-primary" 
			>
		</p>
	</form>
	<script>
		jQuery(document).ready(function($) {


		});
	</script>
	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}
<?php 

function report_daily_new_page_handler() {
	global $wpdb;

	$page['edit'] = TRUE;
	$page['page'] = 'report_daily_add';

	$table_name = $wpdb->prefix . 'reports_daily'; 

	$student = getStudentByID($_REQUEST['student_id']);

	$default = array(
	    'report_id' => '',
	    'student_id' => $_REQUEST['student_id'],
	    'points_id' => '',
	    'date' => current_time( 'Y-m-d' ),
	    'amanah' => array(),
	    'loyal' => array(),
	    'inisiatif' => array(),
	    'fathonah' => array(),
	    'adil' => array()
	);
	$item = $default;

    $selectAmanah = getDailyReportPoints('amanah');
    $selectLoyal = getDailyReportPoints('loyal');
    $selectInisiatif = getDailyReportPoints('inisiatif');
    $selectFathonah = getDailyReportPoints('fathonah');
    $selectAdil = getDailyReportPoints('adil');

    if (isset($_REQUEST['id'])) {
	    $item = $wpdb->get_row(
	      $wpdb->prepare( " 
	          SELECT  
	              r.report_id as report_id,
	              r.student_id as student_id,
	              r.date as date

	          FROM {$wpdb->prefix}students s 
	            LEFT JOIN {$wpdb->prefix}reports_daily r 
	              ON s.student_id = r.student_id
	          WHERE r.report_id = '%d'
	          ", 
	        $_REQUEST['id'] 
	      ), 
	      ARRAY_A
	    );

	  $item['amanah'] = getDailyReportPointsByID($_REQUEST['id'],'amanah');
	  $item['loyal'] = getDailyReportPointsByID($_REQUEST['id'],'loyal');
	  $item['inisiatif'] = getDailyReportPointsByID($_REQUEST['id'],'inisiatif');
	  $item['fathonah'] = getDailyReportPointsByID($_REQUEST['id'],'fathonah');
	  $item['adil'] = getDailyReportPointsByID($_REQUEST['id'],'adil');

	  $item = shortcode_atts($default, $item);

	  if (!$item) {
	      $notice = __('Item not found', 'alifakids');
	  }
	}

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );
	?>

	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" id="dailyReport">
          <input name='action' type="hidden" value='new_report_daily'>
          <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('new_report_daily_admin')?>"/>
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
						<p><?php echo date_i18n("l, d F Y", strtotime( $item['date'] ) ); ?></p>
					</td>
				</tr>
			</tbody>	
		</table>

		<h2 class="title">List Perilaku Anak A.L.I.F.A</h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr class="form-required">
					<td>
						<select 
							multiple="multiple" 
							id="reportAmanah" 
							name="amanah[]"
							class="regular-text select-report" 
						>
							<?php 
								$optgroups = array(
									'integritas' => 'Integritas',
									'tanggungjawab' => 'Tanggung Jawab', 
									'produktif' => 'Produktif'
								);
							?>
							<?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
			                  <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
			                    <?php foreach ($selectAmanah as $value): ?>
			                      <?php if ($value['points_dimension'] == $optgroup_key): ?>
			                          <option 
			                            value="<?php echo $value['points_id'] ?>" 
			                            <?php echo ( in_array( $value['points_id'] , $item['amanah']) ) ? 'selected' : '' ; ?> 
			                          ><?php echo esc_html( $value['name']   ); ?></option>
			                      <?php endif ?>
			                    <?php endforeach ?>
			                  </optgroup>
			                 <?php endforeach ?>
					    </select>
					</td>
				</tr>
				<tr class="form-required">
					<td>
						<select 
							multiple="multiple" 
							id="reportLoyal" 
							name="loyal[]"
							class="regular-text select-report" 
						>
							<?php 
								$optgroups = array(
									'spiritual' => 'Spiritual',
									'tangguh' => 'Tangguh',
									'pengendaliandiri' => 'Pengendalian Diri'
								);
							?>
							<?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
			                  <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
			                    <?php foreach ($selectLoyal as $value): ?>
			                      <?php if ($value['points_dimension'] == $optgroup_key): ?>
			                          <option 
			                            value="<?php echo $value['points_id'] ?>" 
			                           <?php echo ( in_array( $value['points_id'] , $item['loyal']) ) ? 'selected' : '' ; ?> 
			                          ><?php echo esc_html($value['name']) ?></option>
			                      <?php endif ?>
			                    <?php endforeach ?>
			                  </optgroup>
			                <?php endforeach ?>
					    </select>
					</td>
				</tr>
				<tr class="form-required">
					<td>
						<select 
							multiple="multiple" 
							id="reportInisiatif" 
							name="inisiatif[]"
							class="regular-text select-report" 
						>
							<?php 
								$optgroups = array(
									'mandiri' => 'Mandiri',
									'pengambilresiko' => 'Pengambil Resiko',
									'berkolaborasi' => 'Berkolaborasi'
								);
							?>
							<?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
			                  <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
			                    <?php foreach ($selectInisiatif as $value): ?>
			                      <?php if ($value['points_dimension'] == $optgroup_key): ?>
			                          <option 
			                            value="<?php echo $value['points_id'] ?>" 
			                           <?php echo ( in_array( $value['points_id'] , $item['inisiatif']) ) ? 'selected' : '' ; ?> 
			                          ><?php echo esc_html($value['name']) ?></option>
			                      <?php endif ?>
			                    <?php endforeach ?>
			                  </optgroup>
			                <?php endforeach ?>
					    </select>
					</td>
				</tr>
				<tr class="form-required">
					<td>
						<select 
							multiple="multiple" 
							id="reportFathonah" 
							name="fathonah[]"
							class="regular-text select-report" 
						>
							<?php 
								$optgroups = array(
									'intelijen' => 'Intelijen',
									'komunikasi' => 'Komunikasi',
									'kreasi' => 'Kreasi'
								);
							?>
							<?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
			                  <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
			                    <?php foreach ($selectFathonah as $value): ?>
			                      <?php if ($value['points_dimension'] == $optgroup_key): ?>
			                          <option 
			                            value="<?php echo $value['points_id'] ?>" 
			                             <?php echo ( in_array( $value['points_id'] , $item['fathonah']) ) ? 'selected' : '' ; ?> 
			                          ><?php echo esc_html($value['name']) ?></option>
			                      <?php endif ?>
			                    <?php endforeach ?>
			                  </optgroup>
			                <?php endforeach ?>
					    </select>
					</td>
				</tr>
				<tr class="form-required">
					<td>
						<select 
							multiple="multiple" 
							id="reportAdil" 
							name="adil[]"
							class="regular-text select-report" 
						>
							<?php 
								$optgroups = array(
									'kesantunan' => 'kesantunan',
									'menghargai' => 'menghargai',
									'berpikirkritis' => 'Berpikir Kritis'
								);
							?>
							<?php foreach ($optgroups as $optgroup_key => $optgroup): ?>
				                <optgroup label="<?php echo $optgroup ?>" id="<?php echo $optgroup_key ?>">
				                  <?php foreach ($selectAdil as $value): ?>
				                    <?php if ($value['points_dimension'] == $optgroup_key): ?>
				                        <option 
				                          value="<?php echo $value['points_id'] ?>" 
				                           <?php echo ( in_array( $value['points_id'] , $item['adil']) ) ? 'selected' : '' ; ?> 
				                        ><?php echo esc_html($value['name']) ?></option>
				                    <?php endif ?>
				                  <?php endforeach ?>
				                </optgroup>
			                <?php endforeach ?>
					    </select>
					</td>
				</tr>
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
		jQuery(document).ready(function() {

			jQuery("#dailyReport").on('submit', function(event) {

		        var res = true;

		        jQuery(".select-report").each(function(i, e) {

		            jQuery(e).children('optgroup').each(function(index, el) {

		              length = jQuery(el).children('option:selected').length;

		              if (length != 2 && !jQuery(el).is('#intelijen') ){
		                res = false;
		              } else if ( jQuery(el).is('#intelijen') && length != 4  ) {
		                res = false;
		              }

		            });
		        });

		        if (res == false) {
		          jQuery(window).scrollTop(0);
		          alert('Submit Error! Setiap dimensi harus memiliki 2 poin & khusus dimensi intelejensi harus 4 poin.');
		          return res;
		        } else {
		          return res;
		        }

		      });

			jQuery('#reportAmanah').multiSelect({
		        keepOrder: true,
		        selectableHeader: "<div class='ms-header'>Amanah</div>",
		        selectionHeader: "<div class='ms-header'>Amanah</div>",
		        afterSelect: function(values){
		          jQuery.each( jQuery('#reportAmanah optgroup'),function (i,e) {
		            
		            length = jQuery(e).children('option:selected').length;

		            if (length > 2 ) {
		              jQuery('#reportAmanah').multiSelect('deselect',values);
		              return false;
		            };
		          });
		        }
		      });

		      jQuery('#reportLoyal').multiSelect({
		        keepOrder: true,
		        selectableHeader: "<div class='ms-header'>Loyal</div>",
		        selectionHeader: "<div class='ms-header'>Loyal</div>",
		        afterSelect: function(values){
		          jQuery.each( jQuery('#reportLoyal optgroup'),function (i,e) {
		            
		            length = jQuery(e).children('option:selected').length;

		            if (length > 2 ) {
		              jQuery('#reportLoyal').multiSelect('deselect',values);
		              return false;
		            };
		          });
		        }
		      });

		      jQuery('#reportInisiatif').multiSelect({
		        keepOrder: true,
		        selectableHeader: "<div class='ms-header'>Inisiatif</div>",
		        selectionHeader: "<div class='ms-header'>Inisiatif</div>",
		        afterSelect: function(values){
		          jQuery.each( jQuery('#reportInisiatif optgroup'),function (i,e) {
		            
		            length = jQuery(e).children('option:selected').length;

		            if (length > 2 ) {
		              jQuery('#reportInisiatif').multiSelect('deselect',values);
		              return false;
		            };
		          });
		        }
		      });

		      jQuery('#reportFathonah').multiSelect({
		        keepOrder: true,
		        selectableHeader: "<div class='ms-header'>Fathonah</div>",
		        selectionHeader: "<div class='ms-header'>Fathonah</div>",
		        afterSelect: function(values){
		          jQuery.each( jQuery('#reportFathonah optgroup'),function (i,e) {
		            length = jQuery(e).children('option:selected').length;

		            if ( jQuery(e).is('#intelijen') ){
		              max_length = 4;
		            } else {
		              max_length = 2;
		            }

		            if (length > max_length ) {
		              jQuery('#reportFathonah').multiSelect('deselect',values);
		              return false;
		            }
		          });
		        }
		      });

		      jQuery('#reportAdil').multiSelect({
		        keepOrder: true,
		        selectableHeader: "<div class='ms-header'>Adil</div>",
		        selectionHeader: "<div class='ms-header'>Adil</div>",
		        afterSelect: function(values){
		          jQuery.each( jQuery('#reportAdil optgroup'),function (i,e) {
		            
		            length = jQuery(e).children('option:selected').length;

		            if (length > 2 ) {
		              jQuery('#reportAdil').multiSelect('deselect',values);
		              return false;
		            };
		          });
		        }
		      });

			jQuery('#student').select2({
				ajax: {
				    url: ajaxurl,
				    dataType: 'json',
				    delay: 250,
				  	data: function (params) {
		                return {
		                    q: params.term,
		                    action: 'ajax_select_students'
		                };
		            },
		            processResults: function( data ) {
						var options = [];
						if ( data ) {
		 
							jQuery.each( data, function( index, text ) { 
								options.push( { id: text[0], text: text[1]  } );
							});
		 
						}
						return {
							results: options
						};
					},
			  	}
			});

			var studentSelect = jQuery('#student');
			jQuery.ajax({
			    type: 'GET',
			    dataType: 'json',
			    url: ajaxurl
			}).then(function (data) {
			    // create the option and append to Select2
			    var option = new Option(data.full_name, data.id, true, true);
			    studentSelect.append(option).trigger('change');

			    // manually trigger the `select2:select` event
			    studentSelect.trigger({
			        type: 'select2:select',
			        params: {
			            data: data
			        }
			    });
			});

			jQuery('#date').datepicker(jQuery.extend({
			      showMonthAfterYear: false,
			    }, jQuery.datepicker.regional['id']
		  	)).datepicker("setDate", new Date());

		});
	</script>
	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}
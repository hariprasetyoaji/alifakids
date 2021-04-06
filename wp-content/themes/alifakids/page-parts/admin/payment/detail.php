<?php 
function payment_detail_page_handler() {
	global $wpdb;

	$page['edit'] = TRUE;
	$page['page'] = 'payment_add';

	$table_name = $wpdb->prefix . 'payment'; 

	$default = array(
        'payment_id' => 0,
        'student_id' => $_REQUEST['student_id'],
        'period' => $_REQUEST['period'],
        'date' => '',
        'amount' => '',
        'sender' => '',
        'transfer_to' => '',
        'status' => '',
        'image' => ''

    );

    $student = getStudentByID($_REQUEST['student_id']);

    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        if ($item['payment_id'] == 0) {

        	$insert_args = array(
		        'student_id' => $_REQUEST['student_id'],
		        'period' => $_REQUEST['period'],
		        'date' => $_REQUEST['date'],
		        'amount' => $_REQUEST['amount'],
		        'sender' => $_REQUEST['sender'],
		        'transfer_to' => $_REQUEST['transfer_to'],
		        'status' => 1
		    );

            $result = $wpdb->insert( $table_name, $insert_args );

            $item['payment_id'] = $wpdb->insert_id;

            if ($_FILES['image']['name'] != "") {
	    		$attachment = $_FILES['image'];
				$wordpress_upload_dir = wp_upload_dir();
			    $new_file_path = $wordpress_upload_dir['path'] . '/' . $attachment['name'];
			    $new_file_mime = mime_content_type( $attachment['tmp_name'] );

			    if( $attachment['size'] > wp_max_upload_size() )
					die( 'It is too large than expected.' );
			 
				if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
					die( 'WordPress doesn\'t allow this type of uploads.' );
		    	
		    	if( move_uploaded_file( $attachment['tmp_name'], $new_file_path ) ) {
					$upload_id = wp_insert_attachment( array(
						'guid'           => $new_file_path, 
						'post_mime_type' => $new_file_mime,
						'post_title'     => preg_replace( '/\.[^.]+$/', '', $attachment['name'] ),
						'post_content'   => '',
						'post_status'    => 'inherit'
					), $new_file_path );

					wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

					$wpdb->update ( 
						$table_name, 
						array('image' => $upload_id ), 
						array('payment_id' => $item['payment_id'] )
					);
		 
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
		    		$item['image'] = $upload_id;

		    	}	
	    	}

            if ($result) {
                $message = __('Item was successfully saved', 'alifakids');
            } else {
                $notice = __('There was an error while saving item', 'alifakids');
            }
        } else {
        	$update_args = array(
		        'student_id' => $_REQUEST['student_id'],
		        'period' => $_REQUEST['period'],
		        'date' => $_REQUEST['date'],
		        'amount' => $_REQUEST['amount'],
		        'sender' => $_REQUEST['sender'],
		        'transfer_to' => $_REQUEST['transfer_to'],
		        'status' => 1
		    );

            $result = $wpdb->update ( 
				$table_name, 
				$update_args, 
				array('payment_id' => $item['payment_id'])
			);

            if ($_FILES['image']['name'] != "") {
	    		$attachment = $_FILES['image'];
				$wordpress_upload_dir = wp_upload_dir();
			    $new_file_path = $wordpress_upload_dir['path'] . '/' . $attachment['name'];
			    $new_file_mime = mime_content_type( $attachment['tmp_name'] );

			    if( $attachment['size'] > wp_max_upload_size() )
					die( 'It is too large than expected.' );
			 
				if( !in_array( $new_file_mime, get_allowed_mime_types() ) )
					die( 'WordPress doesn\'t allow this type of uploads.' );
		    	
		    	if( move_uploaded_file( $attachment['tmp_name'], $new_file_path ) ) {
					$upload_id = wp_insert_attachment( array(
						'guid'           => $new_file_path, 
						'post_mime_type' => $new_file_mime,
						'post_title'     => preg_replace( '/\.[^.]+$/', '', $attachment['name'] ),
						'post_content'   => '',
						'post_status'    => 'inherit'
					), $new_file_path );

					wp_update_attachment_metadata( $upload_id, wp_generate_attachment_metadata( $upload_id, $new_file_path ) );

					$wpdb->update ( 
						$table_name, 
						array('image' => $upload_id ), 
						array('payment_id' => $item['payment_id'] )
					);
		 
					require_once( ABSPATH . 'wp-admin/includes/image.php' );
		    		$item['image'] = $upload_id;
		    	}	
	    	} else {
		    		$item['image'] = $_REQUEST['image_id'];
	    	}


            if ($result) {
                $message = __('Item was successfully updated', 'alifakids');
            } else {
                $notice = __('There was an error while updating item', 'alifakids');
            }
        }
    }
    else {
        
        $item = $default;
        if ($_REQUEST['payment_id']) {
            $item = $wpdb->get_row(
            	$wpdb->prepare( " 
	            		SELECT *
	            		FROM $table_name 
	            		WHERE payment_id = %d 
        			", 
            		$_REQUEST['payment_id'] 
            	), 
            	ARRAY_A
            );


            if (!$item) {
                $item = $default;
                $notice = __('Item not found', 'alifakids');
            }
        }
    }

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );
	?>
	<form method="post" name="createpayment" id="createpayment" enctype="multipart/form-data">
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
		<input type="hidden" name="payment_id" value="<?php echo $item['payment_id'] ?>"/>
		<input type="hidden" name="student_id" value="<?php echo $item['student_id'] ?>"/>
		<input type="hidden" name="image_id" value="<?php echo $item['image'] ?>"/>
		<input type="hidden" name="period" value="<?php echo date('Y-m-01', strtotime($item['period'])); ?>"/>
		<input id="actualDate" type="hidden" name="date" value="<?php echo $item['date'] ?>"/>

		<table class="form-table" role="presentation">
			<tbody>
				<tr class="form-required">
					<th scope="row">
						<label for="name">
							Nama Siswa
						</label>
					</th>
					<td>
						<?php echo $student->name ?>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="name">
							Cabang
						</label>
					</th>
					<td>
						<?php echo getBranchName($student->branch_id) ?>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="name">
							Kelas
						</label>
					</th>
					<td>
						<?php echo getClassName($student->class_id) ?>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="phone">
							Bulan
						</label>
					</th>
					<td>
						<?php echo date_i18n("F Y", strtotime( $item['period'] ) ) ?>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="date">
							Tanggal Pembayaran
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							id="date" 
							type="text" 
							class="regular-text datepicker" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
							required
						/>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="paymentAmount">
							Jumlah Pembayaran
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="amount" 
							id="paymentAmount" 
							value="<?php echo $item['amount'] ?>"
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
							required
						/>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="email">
							Bank & Nama Rekening Pengirim
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="sender" 
							id="email" 
							value="<?php echo $item['sender'] ?>"
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
							required
						/>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="address">
							Di Transfer ke
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="transfer_to" 
							id="address" 
							value="<?php echo $item['transfer_to'] ?>"
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="128"
							required
						/>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="address">
							Bukti Pembayaran
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<?php if (!empty($item['image'])): ?>
							<?php echo wp_get_attachment_image($item['image'],'large'); ?>
							<br>
						<?php endif ?>
						<input id="frontend-button" name="image" type="file" value="Upload Foto" class="form-control btn btn-secondary" accept="image/x-png,image/jpeg,,image/jpg">
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
		jQuery(document).ready(function($) {
			var date = new Date();

			if (jQuery('#actualDate').val() != '') {
				date = new Date(jQuery('#actualDate').val());
			}

			jQuery('.datepicker').datepicker(
		        jQuery.extend( {
		            showMonthAfterYear: false,
		            altFormat: "yy-mm-dd",
		            altField: "#actualDate"
		          }, jQuery.datepicker.regional['id']
		        )
		  	).datepicker("setDate", date);

		  	jQuery('#paymentAmount').change(function(event) {
				/* Act on the event */
				var number = jQuery(this).val();
				if(typeof number != 'undefined'){
					if(!(is_numeric(number) && number != '')){
						number = '0';
					}
				}else{
					number = '0';
				}
				if(number < 1){
					number = '0';
				}
				jQuery(this).val(number);
				//$('#amount_unlock').val(number);
			});
			function is_numeric(input){
				return !isNaN(input);
			}
		});
	</script>
	<?php
}

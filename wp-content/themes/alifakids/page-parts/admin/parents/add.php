<?php 

function parents_new_page_handler() {
	global $wpdb;

	$page['edit'] = TRUE;
	$page['page'] = 'parents_add';

	$table_name = $wpdb->prefix . 'users'; 



	$default = array(
        'ID' => '',
        'user_login' => '',
        'user_email'      => '',
        'first_name'  => '',
        'last_name'  => '',
        'birth_place'  => '',
        'birth_date'  => '',
        'address'  => '',
        'religion'  => '',
        'education'  => '',
        'occupation'  => '',
        'office_address'  => '',
        'office_address'  => '',
        'social_media'  => '',
        'phone'  => ''
    );

    $students = array();

 	$item = $default;
    if (isset($_REQUEST['id'])) {
        $item = $wpdb->get_row(
	    	$wpdb->prepare( " 
	    			SELECT 	u.ID as ID,
						    u.user_login AS user_login,
						    u.user_pass AS user_pass,
						    u.user_email AS user_email,
						    (select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'first_name' limit 1) as first_name,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'last_name' limit 1) as last_name,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'address' limit 1) as address,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'phone' limit 1) as phone,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'birth_place' limit 1) as birth_place,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'birth_date' limit 1) as birth_date,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'religion' limit 1) as religion,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'education' limit 1) as education,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'social_media' limit 1) as social_media,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'occupation' limit 1) as occupation,
							(select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'office_address' limit 1) as office_address,
							GROUP_CONCAT(ps.student_id) as student_id
					FROM {$wpdb->prefix}users as u
						LEFT OUTER JOIN {$wpdb->prefix}parents_students ps
							ON u.ID = ps.parent_id
					WHERE (select meta_value from {$wpdb->prefix}usermeta where user_id = u.id and meta_key = 'ak_capabilities' limit 1) LIKE '%parent%'
					AND u.ID = %d 
				", 
	    		$_REQUEST['id'] 
	    	), 
	    	ARRAY_A
	    );

		$students = getParentStudents($item['ID']);

	    if (!$item) {
	        $item = $default;
	        $notice = __('Item not found', 'alifakids');
	    }
    }


	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );
	?>

	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" name="createparent" id="createparent">
		<input name='action' type="hidden" value='ak_form_parent'>
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('ak_form_parent')?>"/>
		<input type="hidden" name="user_id" value="<?php echo $item['ID'] ?>"/>

		<table class="form-table" role="presentation">
			<tbody>
				<tr class="form-required">
					<th scope="row">
						<label for="user_login">
							Username
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="user_login" 
							id="user_login" 
							value="<?php echo $item['user_login'] ?>"
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
							required
							<?php echo (isset($_REQUEST['id'])) ? 'disabled' : '' ; ?>
						/>
						<?php echo (isset($_REQUEST['id'])) ? '<span class="description">Usernames cannot be changed.</span>' : '' ; ?>

					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="user_email">
							Email
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="user_email" 
							id="user_email" 
							value="<?php echo $item['user_email'] ?>"
							type="email" 
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
						<label for="first_name">
							First Name
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="first_name" 
							id="first_name" 
							value="<?php echo $item['first_name'] ?>"
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
						<label for="last_name">
							Last Name
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="last_name" 
							id="last_name" 
							value="<?php echo $item['last_name'] ?>"
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


				<tr id="password" class="form-required user-pass1-wrap">
					<th scope="row">
						<label for="pass1">
							<?php _e( 'Password' ); ?>
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<button type="button" class="button wp-generate-pw hide-if-no-js"><?php _e( 'Generate Password' ); ?></button>

						<div class="wp-pwd hide-if-js">
							<span class="password-input-wrapper">
								<input type="password" name="pass1" id="pass1" class="regular-text" value="" autocomplete="off" data-pw="<?php echo esc_attr( wp_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" required/>
							</span>
							<button type="button" class="button wp-hide-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
								<span class="dashicons dashicons-hidden"></span>
								<span class="text"><?php _e( 'Hide' ); ?></span>
							</button>
							<button type="button" class="button wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change' ); ?>">
								<span class="text"><?php _e( 'Cancel' ); ?></span>
							</button>
							<div style="display:none" id="pass-strength-result" aria-live="polite"></div>
						</div>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="birth">
							Tempat / Tanggal Lahir
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="birth_place" 
							id="birth-place" 
							value="<?php echo $item['birth_place'] ?>"
							type="text" 
							class="birth-place-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="20"
							required
						/> /
						<input 
							id="birth-date" 
							value="<?php echo $item['birth_date'] ?>"
							type="text" 
							class="birth-date-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="20"
							required
						/>
						<input type="hidden" name="birth_date" id="actualDateBirth" value="<?php echo $item['birth_date']; ?>">
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="phone">
							Phone
						</label>
					</th>
					<td>
						<input 
							name="phone" 
							id="phone" 
							value="<?php echo $item['phone'] ?>"
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
						/>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="address">
							Alamat
						</label>
					</th>
					<td>
						<textarea 
							name="address" 
							id="address" 
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="128"
						/><?php echo $item['address'] ?></textarea>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="religion">
							Agama
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<select 
							name="religion" 
							id="religion" 
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							required
						/>
							<option value="">Agama</option>
							<option value="Islam" <?php echo ($item['religion'] == 'Islam') ? 'selected' : '' ; ?> >Islam</option>
							<option value="Kristen" <?php echo ($item['religion'] == 'Kristen') ? 'selected' : '' ; ?> >Kristen</option>
							<option value="Katolik" <?php echo ($item['religion'] == 'Katolik') ? 'selected' : '' ; ?> >Katolik</option>
							<option value="Hindu" <?php echo ($item['religion'] == 'Hindu') ? 'selected' : '' ; ?> >Hindu</option>
							<option value="Buddha" <?php echo ($item['religion'] == 'Buddha') ? 'selected' : '' ; ?> >Buddha</option>
							<option value="Khonghucu" <?php echo ($item['religion'] == 'Khonghucu') ? 'selected' : '' ; ?> >Khonghucu</option>
						</select>

					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="education">
							Pendidikan
						</label>
					</th>
					<td>
						<input 
							name="education" 
							id="education" 
							value="<?php echo $item['education'] ?>"
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
						/>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="occupation">
							Pekerjaan
						</label>
					</th>
					<td>
						<input 
							name="occupation" 
							id="occupation" 
							value="<?php echo $item['occupation'] ?>"
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
						/>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="office_address">
							Alamat Kantor
						</label>
					</th>
					<td>
						<textarea 
							name="office_address" 
							id="office_address" 
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="128"
						/><?php echo $item['office_address'] ?></textarea>
					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="social_media">
							Akun Sosial Media
						</label>
					</th>
					<td>
						<input 
							name="social_media" 
							id="social_media" 
							value="<?php echo $item['social_media'] ?>"
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
						/>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="students">
							Siswa
						</label>
					</th>
					<td>
						<select 
							name="students[]" 
							id="students" 
							multiple="multiple"
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
						/>	
						<?php foreach ($students as $value): ?>
							<option value="<?php echo $value['student_id'] ?>" selected="selected"><?php echo $value['name'] ?> (<?php echo $value['number'] ?>)</option>
							
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
		jQuery(document).ready(function($) {
			
			var studentSelect = jQuery('#students').select2({
				ajax: {
				    url: ajaxurl,
				    dataType: 'json',
				    delay: 250,
				  	data: function (params) {
		                return {
		                    q: params.term,
		                    student_id: 1,
		                    action: 'select_students'
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

			if( jQuery('#actualDateBirth').val() == '' ) {
					var birth_date = new Date();
				} else {
					var dateAr = jQuery('#actualDateBirth').val().split('-');
					var birth_date = dateAr[2] + '/' + dateAr[1] + '/' + dateAr[0];
				}

				jQuery('#birth-date').datepicker(jQuery.extend({
				      showMonthAfterYear: false,
				      altFormat: "yy-mm-dd",
				      altField: "#actualDateBirth"
				    }, jQuery.datepicker.regional['id']
			  	)).datepicker("setDate", birth_date );
		});

	</script>
	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}
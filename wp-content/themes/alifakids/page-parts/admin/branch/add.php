<?php 

function branch_new_page_handler() {
	global $wpdb;

	$page['edit'] = TRUE;
	$page['page'] = 'branch_add';

	$table_name = $wpdb->prefix . 'branch'; 

	$default = array(
        'branch_id' => '',
        'name' => '',
        'phone'      => '',
        'email'  => '',
        'address'  => ''
    );

    if ( isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
        
        $item = shortcode_atts($default, $_REQUEST);     

        if ($item['branch_id'] == 0) {
            $result = $wpdb->insert( $table_name, $item );
            $item['branch_id'] = $wpdb->insert_id;
            if ($result) {
               wp_redirect(admin_url('admin.php?page=branch&notice=success'));
            } else {
               wp_redirect( admin_url('admin.php?page=branch_add&notice=error') );
            }
        } else {
            $result = $wpdb->update ( 
				$table_name, 
				$item, 
				array('branch_id' => $item['branch_id'])
			);
            if ($result) {
                wp_redirect(admin_url('admin.php?page=branch_add&id='.$item['branch_id'].'&notice=success'));
            } else {
                wp_redirect(admin_url('admin.php?page=branch_add&id='.$item['branch_id'].'&notice=error'));
            }
        }
    }
    else {
        
        $item = $default;
        if (isset($_REQUEST['id'])) {
            $item = $wpdb->get_row(
            	$wpdb->prepare( " 
	            		SELECT *
	            		FROM $table_name 
	            		WHERE branch_id = %d 
        			", 
            		$_REQUEST['id'] 
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



	<form method="post" name="createbranch" id="createbranch">
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
		<input type="hidden" name="branch_id" value="<?php echo $item['branch_id'] ?>"/>

		<table class="form-table" role="presentation">
			<tbody>
				<tr class="form-required">
					<th scope="row">
						<label for="name">
							Nama Cabang
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="name" 
							id="name" 
							value="<?php echo $item['name'] ?>"
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
						<label for="phone">
							Telepon
							<span class="description">(required)</span>
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
							required
						/>
					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="email">
							Email
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="email" 
							id="email" 
							value="<?php echo $item['email'] ?>"
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
							Alamat
							<span class="description">(required)</span>
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
							required
						/><?php echo $item['address'] ?></textarea>
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
		$('#createbranch').validate();
	</script>
	<?php

	get_template_part( 'page-parts/admin/general-after-wrap' );
}
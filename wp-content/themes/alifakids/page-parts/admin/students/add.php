<?php 

function students_new_page_handler() {
	global $wpdb;

	$page['edit'] = TRUE;
	$page['page'] = 'students_add';

	$table_name = $wpdb->prefix . 'students'; 

	$default = array(
        'student_id' => 0,
        'name'      => '',
        'number'  => '',
        'year'  => null,
        'branch_id'  => null,
        'class_id'  => null,
        'gender'  => '',
        'child_number'  => null,
        'father_name'  => '',
        'mother_name'  => '',
        'birth_place'  => '',
        'birth_date'  => '',
        'blood_type'  => '',
        'address'  => '',
        'religion'  => '',
        'hobby'  => ''
    );

    $item = $default;
    if (isset($_REQUEST['student_id'])) {
        $item = $wpdb->get_row(
        	$wpdb->prepare( " 
            		SELECT *
            		FROM $table_name 
            		WHERE student_id = %d 
    			", 
        		$_REQUEST['student_id'] 
        	), 
        	ARRAY_A
        );

        if (!$item) {
            $item = $default;
        }
    }

    $branchData = getBranchSelectOption();
    $classData = getClassSelectOption();
    $academicYear = getAcademicYear();

	include( locate_template( 'page-parts/admin/general-before-wrap.php' ) );
	?>


	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" name="createstudent" id="createstudent">
		<input name='action' type="hidden" value='ak_form_student'>
		<input type="hidden" name="nonce" value="<?php echo wp_create_nonce('ak_form_student')?>"/>
		<input type="hidden" name="student_id" value="<?php echo $item['student_id'] ?>"/>

		<table class="form-table" role="presentation">
			<tbody>
				<tr class="form-required">
					<th scope="row">
						<label for="name">
							Nama 
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
						<label for="number">
							Nomor Induk 
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<input 
							name="number" 
							id="number" 
							value="<?php echo $item['number'] ?>"
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
						<label for="year">
							Tahun Ajaran
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<select 
							name="year" 
							id="year" 
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							maxlength="60"
							required
						/>	
							<?php foreach ($academicYear as $key=>$value): ?>
								<option 
					    			value="<?php echo $value ?>" 
					    			<?php echo ($item['year'] == $value) ? 'selected' : '' ; ?> 
				    			><?php echo $value ?></option>
					    	<?php endforeach ?>
						</select>

					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="branch_id">
							Cabang
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<select 
							name="branch_id" 
							id="branch_id" 
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							required
						/>
							<option value="">Cabang</option>
							<?php foreach ($branchData as $value): ?>
					    		<option 
					    			value="<?php echo $value['branch_id'] ?>" 
					    			<?php echo ($item['branch_id'] == $value['branch_id']) ? 'selected' : '' ; ?> 
				    			><?php echo $value['name'] ?></option>
					    	<?php endforeach ?>
						</select>

					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="branch_id">
							Kelas
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<select 
							name="class_id" 
							id="class_id" 
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							required
						/>
							<option value="">Kelas</option>
							<?php foreach ($classData as $value): ?>
					    		<option 
					    			value="<?php echo $value['class_id'] ?>" 
					    			<?php echo ($item['class_id'] == $value['class_id']) ? 'selected' : '' ; ?> 
				    			><?php echo $value['name'] ?></option>
					    	<?php endforeach ?>
						</select>

					</td>
				</tr>

				<tr class="form-required">
					<th scope="row">
						<label for="gender">
							Jenis Kelamin
							<span class="description">(required)</span>
						</label>
					</th>
					<td>
						<select 
							name="gender" 
							id="gender" 
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
							required
						/>
							<option value="m" <?php echo ($item['gender'] == 'm') ? 'selected' : '' ; ?> >Laki-laki</option>
							<option value="f" <?php echo ($item['gender'] == 'f') ? 'selected' : '' ; ?> >Perempuan</option>
						</select>

					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="child_number">
							Anak ke
						</label>
					</th>
					<td>
						<select 
							name="child_number" 
							id="child_number" 
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
						/>
							<option value="">Anak ke</option>
							<option value="1" <?php echo ($item['child_number'] == '1') ? 'selected' : '' ; ?> >1</option>
							<option value="2" <?php echo ($item['child_number'] == '2') ? 'selected' : '' ; ?> >2</option>
							<option value="3" <?php echo ($item['child_number'] == '3') ? 'selected' : '' ; ?> >3</option>
							<option value="4" <?php echo ($item['child_number'] == '4') ? 'selected' : '' ; ?> >4</option>
							<option value="5" <?php echo ($item['child_number'] == '5') ? 'selected' : '' ; ?> >5</option>
							<option value="6" <?php echo ($item['child_number'] == '6') ? 'selected' : '' ; ?> >6</option>
							<option value="7" <?php echo ($item['child_number'] == '7') ? 'selected' : '' ; ?> >7</option>
						</select>

					</td>
				</tr>

				<tr>
					<th scope="row">
						<label for="father_name">
							Nama Ayah
						</label>
					</th>
					<td>
						<input 
							name="father_name" 
							id="father_name" 
							value="<?php echo $item['father_name'] ?>"
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
						<label for="mother_name">
							Nama Ibu
						</label>
					</th>
					<td>
						<input 
							name="mother_name" 
							id="mother_name" 
							value="<?php echo $item['mother_name'] ?>"
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
						<label for="blood_type">
							Golongan Darah
						</label>
					</th>
					<td>
						<select 
							name="blood_type" 
							id="blood_type" 
							type="text" 
							class="regular-text" 
							aria-required="true" 
							autocapitalize="none" 
							autocorrect="off" 
						/>
							<option value="">Golongan Darah</option>
							<option value="O" <?php echo ($item['blood_type'] == 'O') ? 'selected' : '' ; ?> >O</option>
							<option value="A" <?php echo ($item['blood_type'] == 'A') ? 'selected' : '' ; ?> >A</option>
							<option value="B" <?php echo ($item['blood_type'] == 'B') ? 'selected' : '' ; ?> >B</option>
							<option value="AB" <?php echo ($item['blood_type'] == 'AB') ? 'selected' : '' ; ?> >AB</option>
						</select>

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
						<label for="hobby">
							Hobby
						</label>
					</th>
					<td>
						<input 
							name="hobby" 
							id="hobby" 
							value="<?php echo $item['hobby'] ?>"
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
							maxlength="60"
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
		jQuery(document).ready(function($) {

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
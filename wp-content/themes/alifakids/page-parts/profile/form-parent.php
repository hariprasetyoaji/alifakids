<?php 
	global $current_user;
	global $user_ID;
?>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
    <input name='action' type="hidden" value='edit_profile'>
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('edit_profile')?>"/>
    <input type="hidden" id="actualDateBirth" name="birth_date" value="<?php echo get_user_meta($user_ID,'birth_date',true) ?>"/>

    <div class="form-group">
    	<label for="title">Nama Lengkap</label>
    	<div class="row">
	    	<div class="col-md-6 col-sm-12">
		       	<input type="text" name="first_name" class="form-control" placeholder="Nama Depan" value="<?php echo $current_user->first_name ?>" required>
	    	</div>
	    	<div class="col-md-6 col-sm-12">
	    		<input type="text" name="last_name" class="form-control" placeholder="Nama Belakang" value="<?php echo $current_user->last_name ?>" required>
	    	</div>
    	</div>
    </div>
     <div class="form-group">
    	<label for="title">Email</label>
       	<input type="email" name="user_email" class="form-control" placeholder="Email" value="<?php echo $current_user->user_email ?>" required>
    </div>
     <div class="form-group">
    	<label for="title">Tempat / Tanggal Lahir</label>
    	<div class="row">
	       	<div class="col-md-7 col-sm-12">
		       	<input type="text" name="birth_place" class="form-control" placeholder="Tempat Lahir" value="<?php echo get_user_meta($user_ID,'birth_place',true) ?>" required>
	    	</div>
	    	<div class="col-md-5 col-sm-12">
	    		<input type="text" id="birth-date" class="form-control" placeholder="Tanggal Lahir" value="<?php echo get_user_meta($user_ID,'birth_date',true) ?>" required>
	    	</div>
    	</div>
    </div>

    <div class="form-group">
    	<label for="title">Nomor Telepon / HP</label>
       	<input type="text" name="phone" class="form-control" placeholder="Nomor Telepon / HP" value="<?php echo get_user_meta($user_ID,'phone',true) ?>" required>
    </div>

    <div class="form-group">
    	<label for="title">Alamat</label>
       	<input type="text" name="address" class="form-control" placeholder="Alamat Rumah" value="<?php echo get_user_meta($user_ID,'address',true) ?>">
    </div>

    <div class="form-group">
    	<label for="title">Agama</label>
    	<select name="religion" class="form-control">
    		<option value="">Agama</option>
			<option value="Islam" <?php echo (get_user_meta($user_ID,'religion',true) == 'Islam') ? 'selected' : '' ; ?> >Islam</option>
			<option value="Kristen" <?php echo (get_user_meta($user_ID,'religion',true) == 'Kristen') ? 'selected' : '' ; ?> >Kristen</option>
			<option value="Katolik" <?php echo (get_user_meta($user_ID,'religion',true) == 'Katolik') ? 'selected' : '' ; ?> >Katolik</option>
			<option value="Hindu" <?php echo (get_user_meta($user_ID,'religion',true) == 'Hindu') ? 'selected' : '' ; ?> >Hindu</option>
			<option value="Buddha" <?php echo (get_user_meta($user_ID,'religion',true) == 'Buddha') ? 'selected' : '' ; ?> >Buddha</option>
			<option value="Khonghucu" <?php echo (get_user_meta($user_ID,'religion',true) == 'Khonghucu') ? 'selected' : '' ; ?> >Khonghucu</option>
    	</select>
    </div>

     <div class="form-group">
    	<label for="title">Pendidikan</label>
       	<input type="text" name="education" class="form-control" placeholder="Pendidikan" value="<?php echo get_user_meta($user_ID,'education',true) ?>" required>
    </div> 

    <div class="form-group">
    	<label for="title">Akun Sosial Media</label>
       	<input type="text" name="social_media" class="form-control" placeholder="Akun Sosial Media" value="<?php echo get_user_meta($user_ID,'social_media',true) ?>">
    </div>
    <div class="divider"></div>

    <h5 class="card-title">Pekerjaan</h5>
    <div class="form-group">
    	<label for="title">Pekerjaan</label>
       	<input type="text" name="occupation" class="form-control" placeholder="Pekerjaan" value="<?php echo get_user_meta($user_ID,'occupation',true) ?>">
    </div>
    <div class="form-group">
    	<label for="title">Alamat Kantor</label>
       	<input type="text" name="office_address" class="form-control" placeholder="Alamat Kantor" value="<?php echo get_user_meta($user_ID,'office_address',true) ?>">
    </div>

    <div class="divider"></div>
    <h5 class="card-title">Ubah Password</h5>
    <div class="form-group">
    	<label for="title">Password</label>
		<input type="password" class="form-control" name="password" />
	</div>
	  <div class="form-group">
    	<label for="title">Ketik Ulang Password</label>
		<input type="password" class="form-control" name="password_retyped" />
		<div style="display: none" class="alert" id="password-strength"></div>
	</div>
	<small class="form-text text-muted">Kosongkan jika tidak ingin merubah password.</small>
    <div class="divider"></div>
   

    <button type="submit" class="btn btn-primary">Submit</button>
</form>

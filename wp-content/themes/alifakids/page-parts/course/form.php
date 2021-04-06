<?php 
global $current_user;

$student_id = $_REQUEST['student_id'];
?>

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
    <input name='action' type="hidden" value='new_lesson_report'>
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('new_lesson_report')?>"/>
    <!-- <input type="hidden" name="course_id" value="<?php echo get_the_ID() ?>"/> -->
    <input type="hidden" name="class" value="<?php echo $_REQUEST['class'] ?>"/>
    <input type="hidden" name="year" value="<?php echo $_REQUEST['y'] ?>"/>
    <input type="hidden" name="month" value="<?php echo $_REQUEST['month'] ?>"/>
    <input type="hidden" name="week" value="<?php echo $_REQUEST['week'] ?>"/>
    <input type="hidden" name="day" value="<?php echo $_REQUEST['d'] ?>"/>
    <input type="hidden" name="student_id" value="<?php echo $student_id ?>"/>

    <div class="form-group text-left">
    	<label>1. Pada bagian sesi mana Ananda menunjukkan antusias?</label>
		<input name="point_1" type="text" class="form-control" required="required">
    </div>
    <div class="form-group text-left">
    	<label>2. Apa hal yang sudah berjalan baik pada sesi kali ini?</label>
		<input name="point_2" type="text" class="form-control" required="required">
    </div>
    <div class="form-group text-left">
    	<label>3. Apa aksi perbaikan untuk membersamai Ananda di sesi berikutnya?</label>
		<input name="point_3" type="text" class="form-control" required="required">
    </div>
    <div class="form-group text-left">
    	<label>4. Bagian mana yang sangat dikuasai Ananda?</label>
		<input name="point_4" type="text" class="form-control" required="required">
    </div>
    <div class="form-group text-left">
    	<label>5. Bagian mana yang menantang atau sulit bagi Ananda?</label>
		<input name="point_5" type="text" class="form-control" required="required">
    </div>
    <!-- <div class="form-group">
        <label for="content">Konten</label>
        <?php 

        $argswp = array(
            'textarea_rows' => 10,
            'teeny'         => false,
            'quicktags'     => false,
            'wpautop'       => true,
            'media_buttons' => true,
        );
        wp_editor( '', 'content', $argswp ); ?> 
            
    </div> -->
    <div class="form-group text-left">
        <label for="class">Foto Pembelajaran</label><br>
        <img style="display: block; margin-bottom: 8px;" id="frontend-image" class="img-fluid" /> 
        <input id="frontend-button" name="attachment" type="file" value="Upload Foto" class="form-control btn btn-secondary" accept="image/x-png,image/jpeg,,image/jpg">        
        <small class="form-text text-muted">Ambil foto ananda saat belajar pelajaran ini.</small>

        <input id="attachment-id" type="hidden" name="attachment_id"/>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>


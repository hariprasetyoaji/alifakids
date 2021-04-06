<?php  
	$default = array(
        'ID' => '',
        'post_title' => '',
        'post_content' => '',
        'class_id' => '',
        'level' => '',
        'lesson_id' => '',
        'attachment_id' => ''
    );

    $item = $default;
?>

<div class="col-md-8">
	
	<div class="card">
        <div class="card-body">
        	<h5 class="card-title">Konfirmasi Pembayaran</h5>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
			    <input name='action' type="hidden" value='new_payment_confirmation'>
			    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('new_payment_confirmation')?>"/>
			    <input type="hidden" name="student_id" value="<?php echo $_REQUEST['student_id'] ?>"/>
			    <input type="hidden" name="period" value="<?php echo date('Y-m-01', strtotime($_REQUEST['period'])); ?>"/>
			    <input id="actualDate" type="hidden" name="date" value=""/>

			    <div class="form-group">
			    	<label for="title">Tanggal Pembayaran</label>
			       	<input type="text" class="form-control datepicker" placeholder="Tanggal Pembayaran" value="<?php echo $item['post_title'] ?>" required>
			    </div>
			     <div class="form-group">
			    	<label for="title">Jumlah Pembayaran</label>
			       	<input type="text" name="amount" class="form-control" placeholder="Jumlah Pembayaran" value="<?php echo $item['post_title'] ?>" id="paymentAmount" required>
			       	<small class="form-text text-muted">Isi dengan nominal dana yang dibayarkan.</small>
			    </div>
			     <div class="form-group">
			    	<label for="title">Bank & Nama Rekening Pengirim</label>
			       	<input type="text" name="sender" class="form-control" placeholder="Bank & Nama Rekening Pengirim" value="<?php echo $item['post_title'] ?>" required>
			       	<small class="form-text text-muted">Isi dengan nama pemilik rekening yang digunakan untuk mengirimkan dana.</small>
			    </div>

			    <div class="form-group">
			    	<label for="title">Di Transfer ke</label>
			       	<input type="text" name="transfer_to" class="form-control" placeholder="Di Transfer ke" value="<?php echo $item['post_title'] ?>" required>
			       	<small class="form-text text-muted">Isi dengan rekening tujuan pembayaran Alifa Kids.</small>
			    </div>

			    <div class="form-group">
			        <label for="class">Foto Bukti Pembayaran</label><br>
			        <input id="frontend-button" name="image" type="file" value="Upload Foto" class="form-control btn btn-secondary" accept="image/x-png,image/jpeg,,image/jpg">        
			        <small class="form-text text-muted">Ambil foto bukti pembayaran.</small>

			    </div>
			    <button type="submit" class="btn btn-primary">Submit</button>
			</form>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.datepicker').datepicker(
        $.extend( {
            showMonthAfterYear: false,
            altFormat: "yy-mm-dd",
            altField: "#actualDate"
          }, $.datepicker.regional['id']
        )
  	).datepicker("setDate", new Date());

	$('#paymentAmount').change(function(event) {
		/* Act on the event */
		var number = $(this).val();
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
		$(this).val(number);
		//$('#amount_unlock').val(number);
	});
	function is_numeric(input){
		return !isNaN(input);
	}

});
</script>
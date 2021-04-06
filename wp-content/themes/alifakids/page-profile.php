<?php 
/* Template Name: Profile */
get_header();


/*$item = $wpdb->get_row(
    $wpdb->prepare( "  
            SELECT p.ID as ID,
                    p.post_title as post_title,
                    p.post_content as post_content,
                    tr.term_taxonomy_id as post_category
            FROM {$wpdb->prefix}posts as p 
                INNER JOIN ak_term_relationships tr
                    ON p.ID = tr.object_id
            WHERE p.ID = '%d' AND p.post_type = 'post'
         ", 
        $_REQUEST['id'] 
    ), 
    ARRAY_A
);

$item['attachment_id'] = get_post_thumbnail_id($_REQUEST['id']);

if (!$item) {
    $item = $default;
    $notice = __('Item not found', 'alifakids');
}
     */   
?>

<div class="container">
  <?php get_template_part( '/page-parts/dashboard', 'top' ); ?>
  <div class="row">
	<div class="col-md-4">
  		<?php get_template_part( '/page-parts/dashboard', 'left' ); ?>
	</div>
	<div class="col-md-8">
		<?php echo $flash->show() ?>
      	<div class="card">
	        <div class="card-body">
	        	<h5 class="card-title">Edit Profil</h5>
	        	<?php if (current_user_can( 'parent' )): ?>
	  				<?php get_template_part( '/page-parts/profile/form', 'parent' ); ?>
	        	<?php elseif(current_user_can( 'teacher' )): ?>
	  				<?php get_template_part( '/page-parts/profile/form', 'teacher' ); ?>
	        	<?php endif ?>
	    	</div>
    	</div>
  </div>
</div>
<?php get_footer(); ?>

<script>
	$(document).ready(function() {
		if( $('#actualDateBirth').val() == '' ) {
			var birth_date = new Date();
		} else {
			var dateAr = $('#actualDateBirth').val().split('-');
			var birth_date = dateAr[2] + '/' + dateAr[1] + '/' + dateAr[0];
		}
		$('#birth-date').datepicker($.extend({
		      showMonthAfterYear: false,
		      altFormat: "yy-mm-dd",
		      altField: "#actualDateBirth"
		    }, $.datepicker.regional['id']
	  	)).datepicker("setDate", birth_date );

 
	    // Binding to trigger checkPasswordStrength
	    $( 'body' ).on( 'keyup', 'input[name=password], input[name=password_retyped]',
	        function( event ) {
	        	console.log($('input[name=password]').val());
	        	if ( $('input[name=password]').val() == ''  ) {
	        		$('#password-strength').hide();
	        	} else {
	        		$('#password-strength').show();

	        	}

	            checkPasswordStrength(
	                $('input[name=password]'),         // First password field
	                $('input[name=password_retyped]'), // Second password field
	                $('#password-strength'),           // Strength meter
	                ['black', 'listed', 'word']        // Blacklisted words
	            );
	        }
	    );
	});
	function checkPasswordStrength( $pass1,
                                $pass2,
                                $strengthResult,
                                blacklistArray ) {
	        var pass1 = $pass1.val();
		    var pass2 = $pass2.val();
		 
		    // Reset the form & meter
	        $strengthResult.removeClass( 'short bad good strong mismatch' );
	        $strengthResult.removeClass( 'alert-warning alert-success' );
		 
		    // Extend our blacklist array with those from the inputs & site data
		    blacklistArray = blacklistArray.concat( wp.passwordStrength.userInputBlacklist() )
		 
		    // Get the password strength
		    var strength = wp.passwordStrength.meter( pass1, blacklistArray, pass2 );
		 
		    // Add the strength meter results
		    switch ( strength ) {
		 
		        case 2:
		            $strengthResult.addClass( 'bad alert-warning' ).html( pwsL10n.bad );
		            break;
		 
		        case 3:
		            $strengthResult.addClass( 'good alert-success' ).html( pwsL10n.good );
		            break;
		 
		        case 4:
		            $strengthResult.addClass( 'strong alert-success' ).html( pwsL10n.strong );

		            break;
		 
		        case 5:
		            $strengthResult.addClass( 'mismatch alert-warning' ).html( pwsL10n.mismatch );

		            break;
		 
		        default:
		            $strengthResult.addClass( 'short alert-warning' ).html( pwsL10n.short );

		    }
		 
		    // The meter function returns a result even if pass2 is empty,
		    // enable only the submit button if the password is strong and
		    // both passwords are filled up
		    if($strengthResult.val() != '') $strengthResult.show();

		    return strength;
		}
</script>
<?php 
/* Template Name: Post */
get_header();

global $post;
$parent_id = get_post_ancestors( get_the_ID() )[0];
$parent_slug = get_post_field( 'post_name', $parent_id );
?>
<div class="container">
  <div class="row">
    <div class="col-md-12">
      <div class="page-title">
        <?php the_breadcrumb() ?>
        <h3><?php echo get_the_title(); ?></h3>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-3">
        <?php get_template_part('page-parts/learning/content','sidebar') ?>
    </div>
	<div class="col-md-9">
    <?php echo $flash->show(); ?>
		<div class="card">
            <div class="card-body">
                <?php if ($parent_slug == 'courses'): ?>
                    <?php get_template_part('page-parts/course/content','new') ?>
                <?php elseif($parent_slug == 'learning'): ?>
                    <?php get_template_part('page-parts/learning/content','new') ?>
                <?php endif ?>
            </div>
        </div>
	</div>
</div>
<script>
$(document).ready( function() {
  var file_frame; // variable for the wp.media file_frame
  
  // attach a click event (or whatever you want) to some element on your page
  $( '#frontend-button' ).on( 'click', function( event ) {
    event.preventDefault();

        // if the file_frame has already been created, just reuse it
    if ( file_frame ) {
      file_frame.open();
      return;
    } 

    file_frame = wp.media.frames.file_frame = wp.media({
      title: $( this ).data( 'uploader_title' ),
      button: {
        text: $( this ).data( 'uploader_button_text' ),
      },
      multiple: false// set this to true for multiple file selection
    });

    file_frame.on( 'select', function() {
      attachment = file_frame.state().get('selection').first().toJSON();

      // do something with the file here
      //$( '#frontend-button' ).hide();
      $( '#attachment-id' ).attr('value', attachment.id);
      $( '#frontend-image' ).attr('src', attachment.url);
    });

    file_frame.open();
  });
});
</script>
<?php get_footer(); ?>
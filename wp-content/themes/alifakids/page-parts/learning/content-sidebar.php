<?php
	global $current_user;  
?>
<div class="card card-transparent">
	<div class="card-body">
		<?php if( current_user_can( 'administrator' ) ): ?>
	  		<button  class="btn btn-primary btn-block mb-4" data-toggle="modal" data-target="#newPost">Tambah Baru</button>
	  		<div class="modal fade" id="newPost" tabindex="-1" role="dialog" aria-labelledby="newPostLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <i class="material-icons">close</i>
                            </button>
                        </div>
                        <div class="modal-body">
                        	<div class="row">
                        		<div class="col-lg-6 col-xl-6">
                                    <a href="<?php echo site_url('courses/post'); ?>" class="card file pdf	 card-post-type">
                                        <div class="card-header file-icon">
                                            <i class="material-icons">local_library</i>
                                        </div>
                                        <div class="card-body file-info">
                                            <p>Pelajaran</p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-lg-6 col-xl-6">
                                    <a href="<?php echo site_url('learning/post'); ?>" class="card file card-post-type">
                                        <div class="card-header file-icon">
                                            <i class="material-icons">description</i>
                                        </div>
                                        <div class="card-body file-info">
                                            <p>Artikel</p>
                                        </div>
                                    </a>
                                </div>
                        	</div>
                        </div>
                    </div>
                </div>
            </div>
		<?php endif ?>
	  <div class="file-manager-menu">
	    <ul class="list-unstyled">
	      <li class="fmm-title">Kategori</li>
	      <?php 
	      	$term    = get_queried_object();
			$term_id = ( isset( $term->term_id ) ) ? (int) $term->term_id : 0;

			$cat_by_role = getLearningCategoryByRole($current_user->roles);
			$cat_by_role_arr = explode(',', $cat_by_role);

	      	$categories = get_categories( array(
			    'taxonomy'   => 'category',
			    'orderby'    => 'name',
			    'parent'	=> 0,
			    'hide_empty' => 0
			));

	      	foreach ($categories as $category):
	      		$cat_ID        = (int) $category->term_id;
		        $category_name = $category->name;
		        $cat_class = ( $cat_ID == $term_id ) ? 'active' : '';


	      		if ( strtolower( $category_name ) != 'uncategorized' && in_array($category->slug, $cat_by_role_arr) ):  ?>
					<li>
						<a class="<?php echo $cat_class ?>" href="<?php echo get_category_link( $category->term_id ) ?>">
							<i class="material-icons"><?php echo getCategoryIconBySlug($category->slug) ?></i>
							<?php echo $category->name ?>
						</a>
			      		<ul>
			      		<?php 
			      		$child_categories = get_categories(
					        array(
						        'child_of' => $cat_ID,
						        'orderby' => 'name',
						        'hide_empty' => '0'
					        ));
			      		foreach ($child_categories as $child_category):
			      			$child_cat_class = ( $child_category->term_id == $term_id ) ? 'active' : '';
			      		?>
			      			<li>
			      				<a class="<?php echo $child_cat_class ?>" href="<?php echo get_category_link( $child_category->term_id ) ?>">
			      					<?php echo $child_category->name ?>
		      					</a>
			      			</li>
		      			<?php endforeach ?>
			      		</ul>
					</li>
	      		<?php endif ?>


	      <?php endforeach ?>
	    </ul>
	  </div>
	</div>
</div>
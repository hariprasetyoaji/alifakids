<?php 

function api_learning_parenting(WP_REST_Request $request){
	$data = $request->get_params();

	if (!empty($data['id'])) {
		$post_args = array(
			'p'         => $data['id'],
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'cat' => 4
		);
		$post_query = new WP_Query( $post_args );

		$post = $post_query->posts;

		$post_id = $post[0]->ID;
		$post[0]->ID = "$post_id";
		$post[0]->post_url = get_permalink($post[0]->ID);
		$video_url = get_post_meta( $post[0]->ID, 'video_url', true );

		$post[0]->video_url = ($video_url) ? $video_url : '' ;
		$post[0]->thumb = (get_the_post_thumbnail_url( $post[0]->ID, 'full' )) ? get_the_post_thumbnail_url( $post[0]->ID, 'full' ) : '' ;

		$result['data'] = $post;

		return new WP_REST_Response($result, 200);

	} else {
		$post_args = array(
			'post_type' => 'post',
			'post_status' => 'publish',
			'cat' => 4
		);
		$post_args['posts_per_page'] = (isset($data['limit'])) ? $data['limit'] : get_option( 'posts_per_page' );
		$post_query = new WP_Query( $post_args );


		if ( $post_query->have_posts() ) {
			foreach ($post_query->posts as $post) {
				$post->ID = "$post->ID";
				$post->thumb = (get_the_post_thumbnail_url( $post->ID )) ? get_the_post_thumbnail_url( $post->ID ) : '' ;
				$result['data'][] = $post;
			}
			return new WP_REST_Response($result, 200);
		} else {
			return new WP_Error( 'get_empty', 'No posts found.', array( 'status' => 403 ) );
		}
	}


}
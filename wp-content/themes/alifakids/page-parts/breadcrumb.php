<?php 
function the_breadcrumb() {
    global $post;
    echo '<nav aria-label="breadcrumb">';
    echo '<ol class="breadcrumb breadcrumb-separator-1">';
    if (!is_home()) {
        echo '<li class="breadcrumb-item"><a href="';
        echo get_option('home');
        echo '">';
        echo 'Dashboard';
        echo '</a></li>';
        if (is_category() || is_single() || is_tag() || is_tax('lesson') ) {
            
            if (is_category()) {
                echo '<li class="breadcrumb-item"><a href="'.site_url('learning').'">Learning</a></li>';
                $curr_cat = get_category(get_query_var('cat'));
                echo '<li class="breadcrumb-item active">'.$curr_cat->name.'</li>';
            } else if (is_tag()){
                $curr_tag = get_tag(get_query_var('tag_id'));
                echo '<li class="breadcrumb-item active">'.$curr_tag->name.'</li>';
            } else if (is_tax('lesson')){
                $class_id = $_GET['class'];
                $surl_args =  array(
                    'class' => $_REQUEST['class']
                );

                echo '<li class="breadcrumb-item"><a href="'.site_url('learning').'">Learning</a></li>';
                if (is_parent()) {
                    $student = getStudentByID($_REQUEST['student_id']);
                    $surl_args['student_id'] =  $_REQUEST['student_id'];

                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $surl_args ,site_url('/course') ).'">'.$student->name.'</a></li>';

                } else {
                    echo '<li class="breadcrumb-item"><a href="'.site_url('courses/?class='.$class_id).'">Course</a></li>';
                }
                   
              
                $nama_bulan = array(
                    '1' => 'Januari',
                    '2' => 'Februari',
                    '3' => 'Maret',
                    '4' => 'April',
                    '5' => 'Mei',
                    '6' => 'Juni',
                    '7' => 'Juli',
                    '8' => 'Agustus',
                    '9' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember'
                );
                   

               if ( isset($_REQUEST['y']) && isset($_REQUEST['m']) && isset($_REQUEST['w']) && isset($_REQUEST['d']) ) {
                    $yurl_args = $surl_args;
                    $yurl_args['y'] = $_REQUEST['y'];

                    $murl_args = $yurl_args;
                    $murl_args['month'] = $_REQUEST['m'];

                    $wurl_args = $murl_args;
                    $wurl_args['week'] = $_REQUEST['w'];

                    $durl_args = $wurl_args;
                    $durl_args['d'] = $_REQUEST['d'];


                    
                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $yurl_args ,site_url('/course') ).'">'.$_REQUEST['y'].'</a></li>';
                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $murl_args ,site_url('/course') ).'">'.$nama_bulan[$_REQUEST['m']].'</a></li>';
                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $wurl_args ,site_url('/course') ).'">Minggu '.$_REQUEST['m'].'</a></li>';
                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $durl_args ,site_url('/course') ).'">Day '.$_REQUEST['d'].'</a></li>';
               }

                echo '<li class="breadcrumb-item active">'.get_queried_object()->name.'</li>';
            }
            if( is_single() && get_post_type( $post->ID ) == 'course' ) {
                $class_id = get_post_meta( $post->ID, 'class_id', true );

                $lesson = wp_get_post_terms($post->ID, 'lesson');

                //$class_id = $_GET['class'];
                $surl_args =  array(
                    'class' => $class_id
                );

                echo '<li class="breadcrumb-item"><a href="'.site_url('learning').'">Learning</a></li>';
                if (is_parent()) {
                    $student = getStudentByID($_REQUEST['student_id']);
                    $surl_args['student_id'] =  $_REQUEST['student_id'];

                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $surl_args ,site_url('/course') ).'">'.$student->name.'</a></li>';

                } else {
                    echo '<li class="breadcrumb-item"><a href="'.site_url('courses/?class='.$class_id).'">Course</a></li>';
                }
                   
              
                $nama_bulan = array(
                    '1' => 'Januari',
                    '2' => 'Februari',
                    '3' => 'Maret',
                    '4' => 'April',
                    '5' => 'Mei',
                    '6' => 'Juni',
                    '7' => 'Juli',
                    '8' => 'Agustus',
                    '9' => 'September',
                    '10' => 'Oktober',
                    '11' => 'November',
                    '12' => 'Desember'
                );

                $m_class = get_post_meta( $post->ID, 'class', true );
                $m_year = get_post_meta( $post->ID, 'year', true );
                $m_month = get_post_meta( $post->ID, 'month', true );
                $m_week = get_post_meta( $post->ID, 'week', true );
                $m_day = get_post_meta( $post->ID, 'day', true );
                   

               //if ( isset($_REQUEST['year']) && isset($_REQUEST['month']) && isset($_REQUEST['week']) && isset($_REQUEST['day']) ) {
               if ( $m_year && $m_month && $m_week && $m_day ) {
                    $yurl_args = $surl_args;
                    $yurl_args['y'] = $m_year;

                    $murl_args = $yurl_args;
                    $murl_args['month'] = $m_month;

                    $wurl_args = $murl_args;
                    $wurl_args['y'] = $m_year;
                    $wurl_args['month'] = $m_month;
                    $wurl_args['week'] = $m_week;

                    $durl_args = $wurl_args;
                    $durl_args['y'] = $m_year;
                    $durl_args['month'] = $m_month;
                    $durl_args['week'] = $m_week;
                    $durl_args['d'] = $m_day;

                    $curl_args = $durl_args;
                    $curl_args['y'] =$m_year;
                    $curl_args['m'] = $m_month;
                    $curl_args['w'] = $m_week;
                    $curl_args['d'] = $m_day;


                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $yurl_args ,site_url('/course') ).'">'.$m_year.'</a></li>';
                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $murl_args ,site_url('/course') ).'">'.$nama_bulan[$m_month].'</a></li>';
                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $wurl_args ,site_url('/course') ).'">Minggu '.$m_week.'</a></li>';
                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $durl_args ,site_url('/course') ).'">Day '.$m_day.'</a></li>';
                    echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $curl_args ,site_url('/lesson/'.$lesson[0]->slug.'') ).'">'.$lesson[0]->name.'</a></li>';
               }

                echo '<li class="breadcrumb-item active">';
                echo get_the_title();
                echo '</li>';


               /* echo '<li class="breadcrumb-item"><a href="'.site_url('learning').'">Learning</a></li>';
                if (is_parent()) {
                    echo '<li class="breadcrumb-item"><a href="'.site_url('courses/?class='.$class_id.'&student_id='.$_GET['student_id']).'">Course</a></li>';
                    echo '<li class="breadcrumb-item"><a href="'.site_url('lesson/'.$lesson[0]->slug.'/?class='.$class_id).'&student_id='.$_GET['student_id'].'">'.$lesson[0]->name.'</a></li>';

                } else {
                    echo '<li class="breadcrumb-item"><a href="'.site_url('courses/?class='.$class_id).'">Course</a></li>';
                    echo '<li class="breadcrumb-item"><a href="'.site_url('lesson/'.$lesson[0]->slug.'/?class='.$class_id).'">'.$lesson[0]->name.'</a></li>';
                }


                echo '<li class="breadcrumb-item active">';
                echo get_the_title();
                echo '</li>';*/
            } else if ( is_single() ) {
                echo '<li class="breadcrumb-item"><a href="'.site_url('learning').'">Learning</a></li>';
                echo '<li class="breadcrumb-item active">';
                echo get_the_title();
                echo '</li>';
            } 
        } elseif (is_page()) {
            if($post->post_parent){
                $anc = get_post_ancestors( $post->ID );
                $title = get_the_title();
                foreach ( $anc as $ancestor ) {
                    $output = '<li class="breadcrumb-item active" aria-current="page"><a href="'.get_permalink($ancestor).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a></li> ';
                }
                echo $output;
                 echo '<li class="breadcrumb-item active">';
                echo get_the_title();
                echo '</li>';
            } else {
                global $post;
                $post_slug = $post->post_name;
                if ($post_slug == 'courses') {
                   $surl_args =  array(
                        'class' => $_REQUEST['class']
                    );

                   echo '<li class="breadcrumb-item"><a href="'.site_url('/learning').'">Learning</a></li>';

                   if (is_parent()) {
                        $student = getStudentByID($_REQUEST['student_id']);
                        $surl_args['student_id'] =  $_REQUEST['student_id'];

                        echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $surl_args ,site_url('/course') ).'">'.$student->name.'</a></li>';
                   } else {

                   }
                   
                  
                    $nama_bulan = array(
                        '1' => 'Januari',
                        '2' => 'Februari',
                        '3' => 'Maret',
                        '4' => 'April',
                        '5' => 'Mei',
                        '6' => 'Juni',
                        '7' => 'Juli',
                        '8' => 'Agustus',
                        '9' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember'
                     );
                   

                   if ( isset($_REQUEST['y']) && isset($_REQUEST['month']) && isset($_REQUEST['week']) && isset($_REQUEST['d']) ) {
                        $yurl_args = $surl_args;
                        $yurl_args['y'] = $_REQUEST['y'];

                        $murl_args = $yurl_args;
                        $murl_args['month'] = $_REQUEST['month'];

                        $wurl_args = $murl_args;
                        $wurl_args['week'] = $_REQUEST['week'];


                        echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $yurl_args ,site_url('/course') ).'">'.$_REQUEST['y'].'</a></li>';
                        echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $murl_args ,site_url('/course') ).'">'.$nama_bulan[$_REQUEST['month']].'</a></li>';
                         echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $wurl_args ,site_url('/course') ).'">Minggu '.$_REQUEST['week'].'</a></li>';
                        echo '<li class="breadcrumb-item active" aria-current="page">Day '.$_REQUEST['d'].'</li>';
                   }
                   elseif ( isset($_REQUEST['y']) && isset($_REQUEST['month']) && isset($_REQUEST['week']) ) {
                        $yurl_args = $surl_args;
                        $yurl_args['y'] = $_REQUEST['y'];

                        $murl_args = $yurl_args;
                        $murl_args['month'] = $_REQUEST['month'];


                        echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $yurl_args ,site_url('/course') ).'">'.$_REQUEST['y'].'</a></li>';
                        echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $murl_args ,site_url('/course') ).'">'.$nama_bulan[$_REQUEST['month']].'</a></li>';
                        echo '<li class="breadcrumb-item active" aria-current="page">Minggu '.$_REQUEST['week'].'</li>';

                   } elseif ( isset($_REQUEST['y']) && isset($_REQUEST['month']) ) {
                        $yurl_args = $surl_args;
                        $yurl_args['y'] = $_REQUEST['y'];
                        echo '<li class="breadcrumb-item active" aria-current="page"><a href="'.add_query_arg( $yurl_args ,site_url('/course') ).'">'.$_REQUEST['y'].'</a></li>';
                        echo '<li class="breadcrumb-item active" aria-current="page">'.$nama_bulan[$_REQUEST['month']].'</li>';
                   } elseif ( isset($_REQUEST['y']) ) {
                        echo '<li class="breadcrumb-item active" aria-current="page">'.$_REQUEST['y'].'</li>';
                   } else {

                    if (is_parent()) {
                     echo '<li class="breadcrumb-item active" aria-current="page">'.$student->name.'</li>';
                    }
                   }
                } else {
                    echo '<li class="breadcrumb-item active" aria-current="page"> '.get_the_title().'</li>';
                }
            }
        }
    }
    elseif (is_tag()) {single_tag_title();}
    elseif (is_day()) {echo"<li>Archive for "; the_time('F jS, Y'); echo'</li>';}
    elseif (is_month()) {echo"<li>Archive for "; the_time('F, Y'); echo'</li>';}
    elseif (is_year()) {echo"<li>Archive for "; the_time('Y'); echo'</li>';}
    elseif (is_author()) {echo"<li>Author Archive"; echo'</li>';}
    elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<li>Blog Archives"; echo'</li>';}
    elseif (is_search()) {echo"<li>Search Results"; echo'</li>';}
    echo '</ol>';
    echo '</nav>';
}
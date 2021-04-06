<?php 
	global $wpdb;

	$values = array();
    $query_years = $wpdb->get_results("
    	SELECT (select meta_value from {$wpdb->prefix}postmeta where post_id = p.ID and meta_key = 'month' limit 1) as month 
    		from {$wpdb->prefix}posts as p
    		LEFT JOIN {$wpdb->prefix}postmeta as pm 
    			ON  p.ID = pm.post_id
            WHERE (select meta_value from {$wpdb->prefix}postmeta where post_id = p.ID and meta_key = 'class_id' limit 1) = '".$_REQUEST['class']."'
            AND (select meta_value from {$wpdb->prefix}postmeta where post_id = p.ID and meta_key = 'year' limit 1) = '".$_REQUEST['y']."'
            AND (select meta_value from {$wpdb->prefix}postmeta where post_id = p.ID and meta_key = 'month' limit 1)
            GROUP BY p.ID 
    ");
    foreach ($query_years as &$data){
        $values[] = $data->month;
    }

    $values = array_unique($values,SORT_REGULAR);

    //echo "<pre>",print_r($values,1),"</pre>";
?>

<?php 
$icons_url = array( 
    '1' => array(
        'icon' => get_template_directory_uri().'/assets/images/class_toddler.png',
        'bg' => get_template_directory_uri().'/assets/images/class-toddler-bg.png'
      ), 
    '2' => array(
        'icon' => get_template_directory_uri().'/assets/images/class_playgroup.png',
        'bg' => get_template_directory_uri().'/assets/images/class-playgroup-bg.png'
      ), 
    '3' => array(
        'icon' => get_template_directory_uri().'/assets/images/class_tka.png',
        'bg' => get_template_directory_uri().'/assets/images/class-tka-bg.png'
      ), 
    '4' =>array(
        'icon' =>  get_template_directory_uri().'/assets/images/class_tkb.png',
        'bg' =>  get_template_directory_uri().'/assets/images/class-tkb-bg.png'
      ),
    '5' => array(
        'icon' => get_template_directory_uri().'/assets/images/class_toddler.png',
        'bg' => get_template_directory_uri().'/assets/images/class-toddler-bg.png'
      ), 
    '6' => array(
        'icon' => get_template_directory_uri().'/assets/images/class_playgroup.png',
        'bg' => get_template_directory_uri().'/assets/images/class-playgroup-bg.png'
      ), 
    '7' => array(
        'icon' => get_template_directory_uri().'/assets/images/class_tka.png',
        'bg' => get_template_directory_uri().'/assets/images/class-tka-bg.png'
      ), 
    '8' =>array(
        'icon' =>  get_template_directory_uri().'/assets/images/class_tkb.png',
        'bg' =>  get_template_directory_uri().'/assets/images/class-tkb-bg.png'
      ), 
    '9' =>array(
                    'icon' =>  get_template_directory_uri().'/assets/images/class_prasd.png',
                    'bg' =>  get_template_directory_uri().'/assets/images/class-prasd-bg.png'
                  )
      /*, 
    'Modul Pra SD' => array(
        'icon' => get_template_directory_uri().'/assets/images/class_prasd.png',
        'bg' => get_template_directory_uri().'/assets/images/class-prasd-bg.png'
      )*/
  );

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
?>

<div class="col-12">
<?php
foreach ($values as $key => $value): 
	$url_args = array(
		'class' => $_REQUEST['class'], 
		'y' => $_REQUEST['y'], 
		'month' => $value 
	);

	if (is_parent()) {
		$url_args['student_id'] = $_REQUEST['student_id'];
	}
?>
	<a class="card folder" href="<?php echo add_query_arg($url_args,site_url('/courses')); ?>">
	  <div class="card-body">
	      <div class="folder-bg" style="background-image: url('<?php echo $icons_url[$_REQUEST['class']]['bg'] ?>'); "></div>
	      <div class="folder-icon">
	         <img src='<?php echo $icons_url[$_REQUEST['class']]['icon'] ?>' />
	      </div>
	      <div class="folder-info">
	          <div class="folder-name"><?php echo $nama_bulan[$value] ?></div>
	      </div>
	  </div>
	</a>
<?php endforeach ?>
</div>

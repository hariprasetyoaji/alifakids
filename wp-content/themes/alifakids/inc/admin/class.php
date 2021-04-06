<?php 

function getClassSelectOption() {
	global $wpdb;

	$sql = "SELECT * FROM {$wpdb->prefix}class";

	$result = $wpdb->get_results( $sql ,ARRAY_A);

	return $result;
}

function getClassName($id) {
	global $wpdb;

	$column = $wpdb->get_row( $wpdb->prepare(
		"SELECT * FROM {$wpdb->prefix}class WHERE class_id = '%s'",
		$id
	) );

	return $column->name;
}

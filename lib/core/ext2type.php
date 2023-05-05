<?php
//* Adds new/unknown files types

add_filter( 'ext2type', 'pmt_ext2type' );

function pmt_ext2type( $types ) {

	$types['video'][] = 'webm';
	$types['image'][] = 'svg';

	return $types;
}

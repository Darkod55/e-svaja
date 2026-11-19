<?php 
$vip_form = get_field('vip_form', 'option');
?>

@if ($vip_form ?? false)
	{!! apply_filters('the_content_lite', $vip_form) ?? '' !!}
@endif
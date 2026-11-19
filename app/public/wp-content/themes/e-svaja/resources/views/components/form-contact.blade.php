<?php 
$form_contact = get_field('svaja_form_contact', 'option');
?>

@if ($form_contact ?? false)
	{!! apply_filters('the_content_lite', $form_contact) ?? '' !!}
@endif
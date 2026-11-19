<?php 
$form_newsletter = get_field('svaja_form_newsletter', 'option');
?>

@if ($form_newsletter ?? false)
	{!! apply_filters('the_content_lite', $form_newsletter) ?? '' !!}
@endif
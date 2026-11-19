<?php
$form_newsletter = get_field('svaja_form_newsletter', 'option');

if (is_string($form_newsletter) && $form_newsletter !== '') {
    // Shortcodes only. the_content_lite also runs wptexturize, convert_chars
    // and wpautop, which rewrite && inside the Zoho validation <script> as
    // &#038;&#038; and the browser throws a SyntaxError.
    $form_newsletter = do_shortcode($form_newsletter);
    $form_newsletter = \App\homepage_delay_zoho_analytics($form_newsletter);
}
?>

@if ($form_newsletter ?? false)
	{!! $form_newsletter !!}
@endif

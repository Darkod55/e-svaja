<!doctype html>
<html <?php language_attributes(); ?>>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
	<meta name="p:domain_verify" content="535d781b67231b854b2cb48918cb5a2b"/>
    <link rel="preload" href="/wp-content/plugins/woocommerce/assets/fonts/star.woff" as="font" type="font/woff" crossorigin="anonymous">
	  <!-- Pinterest Tag -->
	  <meta name="google-site-verification" content="pDFYi154b60fAfKGj6RrskCumVSB4qNJJJ5iBNufyQI" />
<script>
!function(e){if(!window.pintrk){window.pintrk = function () {
window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var
  n=window.pintrk;n.queue=[],n.version="3.0";var
  t=document.createElement("script");t.async=!0,t.src=e;var
  r=document.getElementsByTagName("script")[0];
  r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");
pintrk('load', '2613863511507', {em: '<user_email_address>'});
pintrk('page');
</script>
<noscript>
<img height="1" width="1" style="display:none;" alt=""
  src="https://ct.pinterest.com/v3/?event=init&tid=2613863511507&pd[em]=<hashed_email_address>&noscript=1" />
</noscript>
<!-- End Pinterest Tag -->
  </head>

  <body <?php body_class(); ?>>
    <?php wp_body_open(); ?>
    <?php do_action('get_header'); ?>

    <div id="app">
      <?php echo view(app('sage.view'), app('sage.data'))->render(); ?>
    </div>

    <?php do_action('get_footer'); ?>
    <?php wp_footer(); ?>
  </body>
</html>

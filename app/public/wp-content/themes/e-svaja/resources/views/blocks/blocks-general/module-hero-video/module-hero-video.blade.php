<?php
$content  = App\Helpers()->getFieldDataOrBlockPlaceholder("content", $fields ?? '', $block ?? []);
$video    = App\Helpers()->getFieldDataOrBlockPlaceholder("video", $fields ?? '', $block ?? []);
$link    = App\Helpers()->getFieldDataOrBlockPlaceholder("link", $fields ?? '', $block ?? []);
$template = array(
  array( 'core/video', array(
    'src' => '/wp-content/uploads/2023/08/billy-bull.mp4',
    'poster' => '/wp-content/uploads/2023/08/Billy-Bull-6.00_01_07_01-2.png'
  ))
);
?>

<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif class="block-{!! App\Helpers()->kebabCase($block['title']) !!} mxh-lg-100 p-rel module-layout-2 ovf-hidden">
  <div class="video-wrapper">
    <InnerBlocks template="<?php echo esc_attr( wp_json_encode( $template ) ); ?>" />
  </div>
  <div class="container-xxl p-clamp zi-1 video-content">
    <div class="d-flex jc-between flex-column flex-md-row ai-start align-items-md-center">
      <div class="hero-title t-white border-start ps-4 ps-md-5 border-branded-vertical">
        {!! apply_filters('the_content_lite', $content) ?? '' !!}
      </div>
      {!! App\Helpers()->getButtonHtml($link, 'btn btn-lg btn-secondary mt-5 mt-md-0' ) !!}
    </div>
  </div>
</section>
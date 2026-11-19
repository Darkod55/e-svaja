<section @if (!empty($block['anchor'])) id="{!! $block['anchor'] !!}" @endif
class="block-{!! App\Helpers()->kebabCase($block['title']) !!} p-clamp-xl-y {!! ($block['className'] ?? false) ? $block['className'] : '' !!}"
>
  <div class="container-xxl p-clamp-x module-one-column">
    <div class="row {!! $block_layout ?? 'flex-row' !!} g-4">
      <div class="col-lg-10">
        <InnerBlocks />
      </div>
    </div>
  </div>
</section>

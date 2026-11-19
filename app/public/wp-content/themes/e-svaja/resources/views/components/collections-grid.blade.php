<?php 
$collections = get_terms(['taxonomy' => 'product_cat']);
?>
@if ($collections ?? false)
  @foreach ($collections as $collection)
    @php $cat_img = (get_field('collection_image', $collection->taxonomy.'_'.$collection->term_id ) ?? false) ? get_field('collection_image', $collection->taxonomy.'_'.$collection->term_id ) : get_term_meta($collection->term_id,'thumbnail_id',true); @endphp
    <div class="col-6 col-lg-4 p-rel col-collection">
      <div class="ratio ratio-1x1 ovf-hidden">
        {!! wp_get_attachment_image($cat_img, 'full', '', ['class' => 'w-100 h-100 img-obj-cover mb-4 img-coll rounded']) !!}
      </div>
      <a href="{!! get_term_link($collection->term_id) !!}" class="h4 t-dark d-block mt-3 stretched-link">{!! $collection->name !!}</a>
    </div>
  @endforeach
@endif
<style>
.img-coll { transition: all 0.3s ease-in-out; }
.col-collection:hover .img-coll { transform: scale(1.1); }
.col-collection:hover a { color: var(--bs-secondary) !important; }
</style>
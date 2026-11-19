<?php

namespace App\Filters\Traits;

if (!trait_exists(__NAMESPACE__.'\\wooCommerce')) {
    trait wooCommerce
    {
        /**
         * All WooCommerce hooks
         */
        public function wcFilters()
        {
            // modify availability status for CTX feeds
            $this->ctxFilterAvailabilityStatus();
			
            // sanitise product images for CTX feeds
            $this->ctxFilterImageUrls();
        }
		
        /**
         * CTX filter to change the order status of products
         *
         * @return void
         */
        private function ctxFilterAvailabilityStatus() : void
        {
            add_filter('woo_feed_filter_product_availability', function ($status, $product, $config) {
                // @todo: consider adding a condition to only do this for specific feeds.
                if ($status === 'backorder') return 'in stock';
                return $status;
            }, 1, 3);
        }

        /**
         * CTX filter to sanitise image URLS
         *
         * @return void
         */
        private function ctxFilterImageUrls() : void
        {
            add_filter('woo_feed_filter_product_feature_image' , function ($image_url, $product, $config) {
                return $this->prependBaseUrl($image_url);
            }, 1, 3);
            add_filter('woo_feed_filter_product_image' , function ($image_url, $product, $config) {
                return $this->prependBaseUrl($image_url);
            }, 1, 3);
            add_filter('woo_feed_filter_product_images' , function ($image_url, $product, $config) {
                return $this->prependBaseUrl($image_url);
            }, 1, 3);
        }

        /**
         * Prepend Base URL if missing
         *
         * @param string $url
         * @return string
         */
        public function prependBaseUrl(string $url) : string
        {
            if (!$url) return $url;
            if (str_starts_with($url, '/')) return home_url($url);
            return $url;
        }
    }
}

<?php

namespace App\Filters;

/**
 * A class to manage 'filter' and 'action' hooks.
 */

use App\Filters\Traits\forms;
use App\Filters\Traits\media;
use App\Filters\Traits\navs;
use App\Filters\Traits\widgets;
use App\Filters\Traits\strings;
use App\Filters\Traits\wooCommerce;

if (!class_exists(__NAMESPACE__.'\\Filters')) {
    /**
     * Class Filters
     *
     * @since ITD Web v1.1
     * @package App
     * @subpackage Filters
     */
    class Filters
    {
        private static $instance;

        use navs;
        use widgets;
        use forms;
        use media;
        use strings;
        use wooCommerce;
        
        /**
         * Filters constructor.
         */
        protected function __construct()
        {
            /**
             * Nav filters
             *
             * Mainly adds Bootstrap classes 'dropdown', 'dropdown-menu', 'nav-link' and 'arrow icon'
             * - uses Trait\navs
             */
            $this->navFilters();

            /**
             * Widget filter
             * - uses Trait\widgets
             */
            // $this->widgetFilterFooterNavs();

            /**
             * Style search form with Bootstrap classes
             * - uses Trait\forms
             */
             $this->searchFormAddBootstrapClasses();

            /**
             * Media filter
             * - uses Trait\media
             */
             $this->responsiveAttachmentImages(false);

            /**
             * Contact Form 7
             */
            // $this->contactForm7_PreventEnqueingAssets();

            /**
             * Filters string with only WP core 'the_content' filters
             */
             $this->coreContentFilter();
             
            
            /**
             * All Woo Commerce hooks
             */
            $this->wcFilters();
        }

        /**
         * Singleton
         *
         * @return Filters
         */
        public static function instance()
        {
            if ( ! ( self::$instance instanceof self ) ) {
                self::$instance = new self;
            }
            return self::$instance;
        }
    }

    Filters::instance();
}

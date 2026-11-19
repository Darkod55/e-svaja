<?php

namespace App\Providers;

use Roots\Acorn\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use function App\helpers;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('svgIcon', function ($expression) {
            return "<?php echo \\App\\Providers\\ThemeServiceProvider::renderSvgIcon({$expression}); ?>";
        });
    }

    /**
     * Render an SVG icon from a pipe-delimited Blade directive expression.
     *
     * @param  string  $icon  e.g. "search|20|20"
     * @return string
     */
    public static function renderSvgIcon($icon)
    {
        $args = explode('|', (string) $icon);
        $icon_args = ['icon' => $args[0]];

        if (isset($args[1])) {
            $icon_args['width'] = (int) $args[1];
        }
        if (isset($args[2])) {
            $icon_args['height'] = (int) $args[2];
        }
        if (isset($args[3])) {
            $icon_args['title'] = $args[3];
        }

        return helpers()->getSvg($icon_args);
    }
}

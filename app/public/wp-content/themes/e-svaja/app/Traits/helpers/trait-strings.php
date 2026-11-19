<?php

namespace App\Helpers\Traits;

trait strings
{
    /**
     * Generate modal trigger data attributes
     *
     * @param null|object|string|int $post
     * @return string
     */
    public function getModalLinkAttrs($post = null) : string
    {
        $post_id = (is_object($post) && isset($post->ID)) ? $post->ID : ($post ??  get_the_ID());
        $permalink = get_permalink($post_id);

        if (!$permalink) return '';

        // Does is need to be more complex than this?
        // the type can be ascertained when fetching the content
        return sprintf('href="%s" data-toggle="modal" data-target="#content" data-content="%s"', $permalink, $post_id);
    }

    /**
     * Generate contact modal trigger data attributes
     *
     * @param string $link
     * @param string $modal
     * @return string
     */
    public function getLocalModalLinkAttrsMaybe(string $link, string $modal = '#contact-0') : string
    {
        // if (!str_contains($link, $modal) && !$this->isHomeUrl($link)) {
        //     if (defined('IS_ONE_PAGER') && IS_ONE_PAGER && $this->isLocalUrl($link)) {
        //         $post_id = url_to_postid($link);
        //         return $this->getModalLinkAttrs($post_id);
        //     }
        //     return sprintf('href="%s"', $link);
        // }
        return str_contains($link, $modal)
            ? sprintf('href="%s" data-toggle="modal"', $link)
            : sprintf('href="%s"', $link);
    }

    /**
     * Check if the url belongs to this site
     *
     * @param string $url
     * @return bool
     */
    public function isLocalUrl(string $url) : bool
    {
        $url_host      = str_replace('www.', '', parse_url($url,PHP_URL_HOST));
        $home_url_host = str_replace('www.', '', parse_url(home_url(),PHP_URL_HOST));
        return $url_host === $home_url_host;
    }

    /**
     * Check if the url is the home url
     *
     * @param string $url
     * @return bool
     */
    public function isHomeUrl(string $url) : bool
    {
        return trim(parse_url($url,PHP_URL_PATH),'/') === trim(parse_url(home_url(),PHP_URL_PATH),'/');
    }

    /**
     * Check if in the editor and if new block, to apply preview content
     * @param $field ,
     * @param $placeholder
     * @return array|string
     */
    public function checkIfEditor($field, $placeholder) : array|string
    {
        if (!function_exists('get_current_screen')) return $field;
        return $field ?: $placeholder;
    }

    /**
     * Checking if all acf fields are not set or empty
     * @param array $fields
     * @return boolean
     */
    public function checkIfValue(array $fields) : bool
    {
        return !empty(array_filter($fields));
    }


    /**
     * Checking the spacing set in the ACF field. If none set, it applies default value
     *
     * @param array $fields an array of strings
     * @return string       returns string of classes or 'section-xl'
     */
    public function checkSpacing(array $fields) : string
    {
        return $this->getSpacing($fields);
    }

    /**
     *
     *
     * @param array $fields
     * @return string
     */
    public function getSpacing(array $fields) : string
    {
        if (empty($fields)) return 'section-xl ';
        return implode(' ', $fields);
    }

    /**
     * Check if a section should be animated or not
     * @param bool $animated
     * @return string
     */
    public function animatedSection(bool $animated = false) : string
    {
        return ($animated ?? false) ? 'anm8' : '';
    }

    /**
     * Create Button
     *
     * @param $link
     * @param string $classes
     * @return bool|string
     */
    public function getButtonHtml($link, string $classes = '') : bool|string
    {
        if (empty($link['url']) || empty($link['title'])) return false;
        if (empty($link['target'])) return sprintf('<a class="%s" href="%s">%s</a>', $classes, $link['url'], $link['title']);
        return sprintf('<a class="%s" href="%s" target="%s">%s</a>', $classes, $link['url'], $link['target'], $link['title']);
    }

    /**
     * Set border radius for section or image
     *
     * @param $field
     * @return string
     */
    public function getBorderRadius($field) : string
    {
        if (empty($field) || !is_array($field) || $field[0] === 'none') return '';
        if (count($field) === 4) return ' rounded';
        $rounded = ' ';
        foreach ($field as $v) $rounded .= "rounded-$v ";
        return $rounded;
    }

    /**
     * Set border top or bottom for section
     *
     * @param $field
     * @return string
     */
    public function getBorder($field) : string
    {
        return (empty($field) || $field === 'border-none') ? '' : $field;
    }

    /**
     * Set text colour based on the background colour
     *
     * @param string $bg
     * @return string
     */
    public function getTextColour(string $bg = 'dark') : string
    {
        return match ($bg) {
            'white' => 'dark',
            default => 'white'
        };
    }

    /**
     * Transform a string into kebab-case
     *
     * @param $text
     * @return string
     */
    public function kebabCase($text) : string
    {
        $final = str_replace(' ', '-', strtolower($text));
        $final = str_ireplace( ['#038;', '&'], '', $final);
        return str_ireplace( '--', '-', $final);
    }

    /**
     * Transform an array of strings and trim multiple separator characters
     *
     * @param string $separator             default is a space
     * @param array|null $array_of_strings
     * @return string                       returns a clean string form the array of strings
     */
    public function implode(string $separator = ' ', ?array $array_of_strings = []) : string
    {
        if (empty($array_of_strings)) return '';
        return trim(preg_replace("/{$separator}{$separator}+/", $separator, implode($separator, $array_of_strings)));
    }

    /**
     * Return the sanitised tel for href:tel
     *
     * @param string $pretty_tel
     * @return string
     */
    public function getSanitisedTel(string $pretty_tel) : string
    {
        if (str_contains($pretty_tel, '+')) {
            $tel = "+" . preg_replace("/[(]0[)]/", "", $pretty_tel);
            return $tel = "+" . preg_replace( "/[^0-9$]/", "", $tel);
        }
        return preg_replace( "/[^0-9$]/", "", $pretty_tel);
    }

    public function bgOptions($bg = 'white', $type = 'primary')
    {
        switch ($bg) {
            case "white":
                $text = 'dark';
                $other = $type;
                $btn = $type;
                $bg = 'bg-white';
                break;
            case "service-pattern":
                $text = 'white';
                $other = 'white';
                $btn = 'light';
                $bg = 'bg-pattern-' . $type;
                break;
            case "pattern":
                $text = 'white';
                $other = 'white';
                $btn = 'light';
                $bg = 'bg-pattern';
                break;
            default: // service based
                $text = 'white';
                $other = 'white';
                $btn = 'light';
                $bg = 'bg-' . $type;
        }
        return (array) ['text' => $text, 'other' => $other, 'btn' => $btn, 'bg' => $bg];
    }
}

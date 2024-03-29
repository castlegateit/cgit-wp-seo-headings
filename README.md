# Castlegate IT WP SEO Headings

SEO Headings provides a function that returns the text of an optimized heading for a WordPress theme:

~~~ php
<h1>
    <a href="<?= home_url('/') ?>">
        <img src="logo.png" alt="<?= cgit_seo_heading() ?>">
    </a>
</h1>
~~~

## Headings

The heading is returned as follows:

1.  The function can be passed an optional string parameter. If this is set and non-empty, the string will be returned unmodified. For example `cgit_seo_heading('Hello World')` will return `Hello World`.

2.  If the function parameter is not set, [Advanced Custom Fields](https://www.advancedcustomfields.com/) is active, and the page is a single post of any public post type, the function will check the value of the SEO heading field of the current post. If this is non-empty, its value will be returned.

3.  If ACF is not active or if the SEO heading field is empty, the function will return the page title. If [Yoast](https://yoast.com/) is active, it will return the title generated by Yoast.

4.  If none of the above attempts have provided a non-empty string, the function will return the site name.

## Filters

The plugin provides the following filters for customization:

*   `cgit_seo_headings_post_types` An array of post types that should display the ACF SEO heading field. By default, this contains all public post types.

*   `cgit_seo_headings_menu_order` An integer representing the menu order of the ACF SEO headings field group. By default, this is set to 500.

*   `cgit_seo_headings_field_group_args` The complete array of parameters used to define the ACF field group. This is applied after the post type and menu order filters described above.

*   `cgit_seo_headings_move_yoast`  A boolean that determines whether the Yoast fields should be moved from their default position above the ACF fields to a position below the ACF fields. The default value is `true`, which means the Yoast fields are moved to the bottom of the screen.

*   `cgit_seo_headings_remove_default_sitename` A boolean that determines whether the site name should be automatically removed from the heading if the Yoast title is used as the heading value. When enabled, any Yoast separator followed by the site name at the end of the string will be removed. Default value is `true`.

## License

Released under the [MIT License](https://opensource.org/licenses/MIT). See [LICENSE](LICENSE) for details.

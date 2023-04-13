# WordPress Meta Box

A utility to set up meta boxes in WordPress

## Installation

Install via composer:

```bash
composer require grottopress/wordpress-meta-box
```

## Usage

### Set up single meta box

Follow the example below to set up a meta box:

```php
<?php
declare (strict_types = 1);

namespace Vendor\Package;

use GrottoPress\WordPress\MetaBox;
use WP_Post;

class MyAwesomeMetaBox
{
    public function setUp()
    {
        \add_action('add_meta_boxes', [$this, 'add'], 10, 2);
        \add_action('save_post', [$this, 'save']);
        \add_action('edit_attachment', [$this, 'save']);
    }

    /**
     * @action add_meta_boxes
     */
    public function add(string $post_type, WP_Post $post)
    {
        if (!($box = $this->box($post))) {
            return;
        }

        $this->metaBox($box)->add();
    }

    /**
     * @action save_post
     * @action edit_attachment
     */
    public function save(int $post_id)
    {
        if (!($box = $this->box(\get_post($post_id)))) {
            return;
        }

        $this->metaBox($box)->save($post_id);
    }

    /**
     * Define your meta box with the `box` method
     */
    private function box(WP_Post $post): array
    {
        // if (\is_post_type_hierarchical($post->post_type)) {
        //     return [];
        // }

        return [
            'id' => 'my-meta-box-1',
            'title' => \esc_html__('My Meta box'),
            'context' => 'side',
            'priority' => 'default',
            'screen' => 'page',
            // 'callback' => function ($arg) {}, // If set, 'fields' is ignored
            // 'callbackArgs' => [], // Passed as `$arg` to 'callback' above. Required if 'callback' is set.
            // 'saveCallback' => function ($post_id, $arg) {}, // Save with this callback instead of default. Required if 'callback' is set.
            // 'saveCallbackArgs' => [], // Passed as `$arg` to 'saveCallback' above.
            'fields' => [ // See https://github.com/grottopress/wordpress-field
                [ // Field 1
                    'id' => 'my-meta-box-field-1',
                    'type' => 'select',
                    'choices' => [
                        'left' => \esc_html__('Left'),
                        'right' => \esc_html__('Right'),
                    ],
                    'label' => \esc_html__('Select direction'),
                    'labelPos' => 'before_field', // or 'after_field'
                    'sanitizeCallback' => 'sanitize_text_field'
                ],
                [ // Field 2
                    'id' => 'my-meta-box-field-2',
                    'type' => 'text',
                    // ...
                ]
            ],
            'notes' => '<p>'.\esc_html__('Just a super cool meta box example').'</p>',
        ];
    }

    private function metaBox(array $args): MetaBox
    {
        return new MetaBox($args);
    }
}

// Add your meta box to WordPress
$myMetaBox = new MyAwesomeMetaBox();
$myMetaBox->setUp();
```

### Set up multiple meta boxes

You may set up multiple meta boxes all at the same time, by returning an array of arrays (an array of meta box args) in the `box()` method in our example above:

```php
// ...
public function box(WP_Post $post)
{
    return [
        [ // Meta box 1
            'id' => 'meta-box-1',
            'context' => 'normal',
            'fields' => [
                [ // Field 1 

                ],
                [ // Field 2

                ]
            ],
            // ...
        ],
        [ // Meta box 2
            'id' => 'meta-box-2',
            'context' => 'side',
            // ...
        ],
        // ...
    ];
}
```

Then, use a foreach loop for both `add()` and `save()` methods:

```php
// ...
public function add(string $post_type, WP_Post $post)
{
    if (!($boxes = $this->box($post))) {
        return;
    }

    foreach ($boxes as $box) {
        $this->metaBox($box)->add();
    }
}

// ...
public function save(int $post_id)
{
    if (!($boxes = $this->box(\get_post($post_id)))) {
        return;
    }

    foreach ($boxes as $box) {
        $this->metaBox($box)->save($post_id);
    }
}
```

## Development

Run tests with `composer run test`.

## Contributing

1. [Fork it](https://github.com/GrottoPress/wordpress-meta-box/fork)
1. Switch to the `master` branch: `git checkout master`
1. Create your feature branch: `git checkout -b my-new-feature`
1. Make your changes, updating changelog and documentation as appropriate.
1. Commit your changes: `git commit`
1. Push to the branch: `git push origin my-new-feature`
1. Submit a new *Pull Request* against the `GrottoPress:master` branch.

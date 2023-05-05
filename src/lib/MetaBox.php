<?php
declare (strict_types = 1);

namespace GrottoPress\WordPress;

use GrottoPress\WordPress\Form\Field;
use WP_Post;

class MetaBox
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|string[int]|\WP_Screen
     */
    protected $screen;

    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $priority;

    /**
     * @var mixed[string]
     */
    protected $fields;

    /**
     * @var callable $callback Callback to pass to `add_meta_box()`
     */
    protected $callback;

    /**
     * @var mixed[mixed] $callbackArgs Callback args to pass to `add_meta_box()`
     */
    protected $callbackArgs = [];

    /**
     * @var callable $saveCallback Callback to save post meta
     */
    protected $saveCallback;

    /**
     * @var mixed $saveCallbackArgs Args to pass to save callback
     */
    protected $saveCallbackArgs;

    /**
     * @var string $notes Notes to add to bottom of meta box.
     */
    protected $notes;

    /**
     * @var string
     */
    protected $nonce;

    /**
     * @param mixed[string] $args
     */
    public function __construct(array $args)
    {
        $this->setAttributes($args);
        $this->sanitizeAttributes();

        $this->nonce = "_wpnonce-{$this->id}";
        $this->callbackArgs['fields'] = $this->fields;
    }

    public function add()
    {
        \add_meta_box(
            $this->id,
            $this->title,
            ($this->callback ?: [$this, 'render']),
            $this->screen,
            $this->context,
            $this->priority,
            $this->callbackArgs
        );
    }

    public function remove()
    {
        \remove_meta_box($this->id, $this->screen, $this->context);
    }

    /**
     * @param mixed[string] $box
     */
    public function render(WP_Post $post, array $box = [])
    {
        if (empty($fields = $box['args']['fields'])) {
            return;
        }

        $html = \wp_nonce_field(\basename(__FILE__), $this->nonce, true, false);

        foreach ($fields as $attr) {
            $attr = $this->sanitizeField($attr);

            if (!empty($attr['key'])) {
                $attr['value'] = \get_post_meta($post->ID, $attr['key']) ?: [];
                $attr['value'] = (\count($attr['value']) < 2) ?
                    ($attr['value'][0] ?? null) : $attr['value'];
            }

            $html .= $this->field($attr)->render();
        }

        if ($this->notes) {
            $html .= $this->notes;
        }

        echo $html;
    }

    public function save(int $post_id = 0)
    {
        if (!$this->preSaveChecksPassed($post_id)) {
            return;
        }

        if ($this->callback || $this->saveCallback) {
            return \call_user_func(
                $this->saveCallback,
                $post_id,
                $this->saveCallbackArgs
            );
        }

        if (!$this->fields) {
            return;
        }

        foreach ($this->fields as $attr) {
            $attr = $this->sanitizeField($attr);

            if (empty($attr['key'])) {
                continue;
            }

            $name = \explode('[', $this->field($attr)->name)[0];

            $content = (array)($_POST[$name] ?? null);

            \delete_post_meta($post_id, $attr['key']);

            if (empty($content[0])) {
                continue;
            }

            foreach ($content as $new_meta_value) {
                if (!empty($attr['sanitizeCallback'])) {
                    $new_meta_value = \call_user_func(
                        $attr['sanitizeCallback'],
                        $new_meta_value
                    );
                } else {
                    $new_meta_value = \sanitize_text_field($new_meta_value);
                }

                \add_post_meta($post_id, $attr['key'], $new_meta_value);
            }
        }
    }

    /**
     * Checks to perform prior save
     */
    protected function preSaveChecksPassed(int $post_id): bool
    {
        if ($post_id < 1) {
            return false;
        }

        if (\defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return false;
        }

        if (!\current_user_can(
            \get_post_type_object(\get_post_type($post_id))->cap->edit_post,
            $post_id
        )) {
            return false;
        }

        if (!isset($_POST[$this->nonce]) ||
            !\wp_verify_nonce($_POST[$this->nonce], \basename(__FILE__))
        ) {
            return false;
        }

        return true;
    }

    /**
     * @param mixed[string] $args
     */
    protected function setAttributes(array $args)
    {
        if (!($vars = \get_object_vars($this))) {
            return;
        }

        unset($vars['nonce']);

        foreach ($vars as $key => $value) {
            $this->{$key} = $args[$key] ?? null;
        }
    }

    protected function sanitizeAttributes()
    {
        $this->id = \sanitize_title($this->id);
        $this->title = \sanitize_text_field($this->title);
        $this->fields = (array)$this->fields;
        $this->callbackArgs = (array)$this->callbackArgs;

        $this->context = (\in_array($this->context, [
            'normal',
            'side',
            'advanced'
        ]) ? $this->context : '');

        $this->priority = (\in_array(
            $this->priority,
            ['high', 'low']
        ) ? $this->priority : '');
    }

    protected function sanitizeField(array $field): array
    {
        $field['id'] = \sanitize_title($field['id'] ?? '');
        $field['key'] = \sanitize_key($field['key'] ?? $field['id']);
        $field['name'] = ($field['name'] ?? '') ?: $field['key'];

        return $field;
    }

    /**
     * @param mixed[string] $args
     */
    private function field(array $args): Field
    {
        return new Field($args);
    }
}

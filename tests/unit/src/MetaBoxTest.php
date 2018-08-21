<?php
declare (strict_types = 1);

namespace GrottoPress\WordPress;

use GrottoPress\WordPress\MetaBox\AbstractTestCase;
use Codeception\Util\Stub;
use tad\FunctionMocker\FunctionMocker;

class MetaBoxTest extends AbstractTestCase
{
    public function _before()
    {
        FunctionMocker::replace(
            ['sanitize_title', 'sanitize_text_field'],
            function (string $text): string {
                return $text;
            }
        );
    }

    /**
     * @dataProvider addProvider
     */
    public function testAdd(string $callback = '')
    {
        $add_meta_box = FunctionMocker::replace('add_meta_box');

        $metaBox = new MetaBox([
            'id' => 'sample-meta-box',
            'title' => 'Sample Meta Box',
            'screen' => 'post',
            'context' => 'side',
            'priority' => 'low',
            'callback' => $callback,
            'callbackArgs' => ['a', 'b', 'c'],
            'fields' => [
                [
                    'id' => 'sample-meta-box-field-1',
                ],
            ],
            'notes' => '<p>Just a super cool layout meta box example</p>',
        ]);

        $metaBox->add();

        $add_meta_box->wasCalledOnce();

        $add_meta_box->wasCalledWithOnce([
            'sample-meta-box',
            'Sample Meta Box',
            $callback ?: [$metaBox, 'render'],
            'post',
            'side',
            'low',
            ['a', 'b', 'c', 'fields' => [['id' => 'sample-meta-box-field-1']]],
        ]);
    }

    public function testRemove()
    {
        $remove_meta_box = FunctionMocker::replace('remove_meta_box');

        $metaBox = new MetaBox([
            'id' => 'sample-meta-box',
            'title' => 'Sample Meta Box',
            'screen' => 'post',
            'context' => 'side',
            'priority' => 'low',
            'fields' => [
                [
                    'id' => 'sample-meta-box-field-1',
                ],
            ],
            'notes' => '<p>Just a super cool layout meta box example</p>',
        ]);

        $metaBox->remove();

        $remove_meta_box->wasCalledOnce();
        $remove_meta_box->wasCalledWithOnce([
            'sample-meta-box',
            'post',
            'side'
        ]);
    }

    public function testRender()
    {
        $this->markTestSkipped();
    }

    /**
     * @dataProvider saveProvider
     */
    public function testSave(
        int $postID,
        string $callback,
        string $saveCallback,
        bool $checksPassed
    ) {
        $metaBox = Stub::construct(MetaBox::class, [[
            'id' => 'sample-metabox',
            'title' => 'Sample Metabox',
            'screen' => 'post',
            'context' => 'side',
            'priority' => 'default',
            'callback' => $callback,
            'callbackArgs' => ['a', 'b', 'c'],
            'saveCallback' => $saveCallback,
            'saveCallbackArgs' => ['a', 'b'],
            'fields' => [
                [
                    'id' => 'sample-metabox-field-1',
                    'type' => 'select',
                    'choices' => [
                        'left' => 'Left',
                        'right' => 'Right',
                    ],
                    'label' => 'Select direction',
                    'label_pos' => 'before_field',
                ],
            ],
            'notes' => '<p>Just a super cool layout metabox example</p>',
        ]], [
            'preSaveChecksPassed' => $checksPassed,
        ]);

        $metaBox->save($postID);
    }

    public function addProvider()
    {
        return [
            'callback is provided' => ['sample_meta_box_callback'],
            'callback is not provided' => [''],
        ];
    }

    public function saveProvider()
    {
        return [

        ];
    }
}

# How to use

1. Check [example layout here](../../templates/editmode/examples.html.twig)
2. ~~Use extension `builder_render_editables(customLayouts)` [all extesions](../../EXTENSION.md) to get all data~~ use variable `builderEditables` instead

# Render editables from Controller

- View function `editableCustom` as example [here](../../src/Controller/ExampleController.php#L32)
- Model construction [here](../../src/Model/Document/Editable)

## Create a `Editable` controller extends `EditableBaseController`

```bash
<?php

namespace App\Controller;

use Starfruit\BuilderBundle\Controller\Document\EditableBaseController;
use Starfruit\BuilderBundle\Model\Document\Editable\EditableCustom;
use Starfruit\BuilderBundle\Model\Document\Editable\CustomLayout;
use Starfruit\BuilderBundle\Model\Document\Editable\LayoutBlock;
use Starfruit\BuilderBundle\Model\Document\Editable\LayoutItem;

class EditableContentController extends EditableBaseController
{
    public static function homeAction()
    {
        $editableCustom = new EditableCustom(
            'Trang chủ',
            [
                new CustomLayout(
                    'Banner',
                    [
                        new LayoutBlock('banner', [
                            new LayoutItem('image', 'image', [
                                'title' => 'Hình ảnh cho máy tính'
                            ]),
                            new LayoutItem('image', 'imageMobile', [
                                'title' => 'Hình ảnh cho điện thoại'
                            ]),
                        ]),
                    ]
                ),
                new CustomLayout(
                    'Nội dung giới thiệu',
                    [
                        new LayoutItem('textarea', 'introText', [
                            'title' => 'Nội dung giới thiệu ngắn',
                            'placeholder' => 'có thể xuống dòng',
                        ]),
                        new LayoutItem('input', 'introButton', [
                            'title' => 'Nội dung nút',
                        ], 4),
                        new LayoutItem('input', 'introLink', [
                            'title' => 'Đường dẫn của nút',
                        ], 8),
                    ]
                ),
                new CustomLayout(
                    'Bản đồ số',
                    [
                    ]
                ),
                new CustomLayout(
                    'Tin tức - Sự kiện',
                    [
                        new LayoutItem('textarea', 'newsEventTitle', [
                            'title' => 'Tiêu đề',
                            'placeholder' => 'có thể xuống dòng',
                        ]),
                    ]
                ),
                new CustomLayout(
                    'Khu du lịch',
                    [
                        new LayoutItem('textarea', 'zoneTitle', [
                            'title' => 'Tiêu đề',
                            'placeholder' => 'có thể xuống dòng',
                        ], 6),
                        new LayoutItem('textarea', 'zoneText', [
                            'title' => 'Nội dung giới thiệu ngắn',
                            'placeholder' => 'có thể xuống dòng',
                        ], 6),
                    ]
                ),
                new CustomLayout(
                    'Thư viện hình ảnh',
                    [
                        new LayoutItem('textarea', 'libraryTitle', [
                            'title' => 'Tiêu đề',
                            'placeholder' => 'có thể xuống dòng',
                        ]),
                    ]
                ),
                new CustomLayout(
                    'Dịch vụ du lịch',
                    [
                        new LayoutItem('textarea', 'serviceTitle', [
                            'title' => 'Tiêu đề',
                            'placeholder' => 'có thể xuống dòng',
                        ], 6),
                        new LayoutItem('textarea', 'serviceText', [
                            'title' => 'Nội dung giới thiệu ngắn',
                            'placeholder' => 'có thể xuống dòng',
                        ], 6),
                    ]
                ),
                new CustomLayout(
                    'Thông tin cần biết',
                    [
                        new LayoutItem('textarea', 'inforTitle', [
                            'title' => 'Tiêu đề',
                            'placeholder' => 'có thể xuống dòng',
                        ], 6),
                        new LayoutItem('textarea', 'inforText', [
                            'title' => 'Nội dung giới thiệu ngắn',
                            'placeholder' => 'có thể xuống dòng',
                        ], 6),
                    ]
                ),
            ]
        );

        $editableCustom = $editableCustom->render();
        return $editableCustom;
    }
}
```

## Create a `Content` controller using `Editable` controller

```bash
<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use App\Controller\EditableContentController;

class ContentController extends FrontendController
{
    public function homeAction()
    {
        $editableCustom = EditableContentController::getEditables();
        return $editableCustom;
    }
}
```

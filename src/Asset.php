<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace mheads\jui;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Asset extends AssetBundle
{
    public $sourcePath = '@vendor/mheads/yii2-jui-improved/assets';
    public $js = [
        'js/mheads.jui.js',
    ];
    public $css = [
    ];
    public $depends = [
    ];
}

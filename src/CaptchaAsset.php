<?php

namespace dynamikaweb\captcha;

class CaptchaAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@vendor/dynamikaweb/yii2-captcha/assets';

    public $css = [
        'captcha.css'
    ];
    
    public $js = [
        'captcha.js'
    ];
}

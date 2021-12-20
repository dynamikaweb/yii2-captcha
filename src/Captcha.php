<?php

namespace dynamikaweb\captcha;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use Yii;

class Captcha extends \yii\widgets\InputWidget
{
    const POS_LEFT = 'left';
    const POS_CENTER = 'center';
    const POS_RIGHT = 'right';

    const SIZE_NORMAL = 'normal';
    const SIZE_COMPACT = 'compact';

    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';

    const DEFAULT_HCAPTCHA_SITEKEY = '10000000-ffff-ffff-ffff-000000000001';
    const DEFAULT_HCAPTCHA_SECRET = '0x0000000000000000000000000000000000000000';
    const DEFAULT_HCAPTCHA_RESPONSE = '20000000-aaaa-bbbb-cccc-000000000002';

    const DEFAULT_RECAPTCHA_SITEKEY = '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI';
    const DEFAULT_RECAPTCHA_SECRET = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';

    const API_CLIENT_HCAPTCHA = '//js.hcaptcha.com/1/api.js?';
    const API_VERIFY_HCAPTCHA = 'https://hcaptcha.com/siteverify?';

    const API_CLIENT_RECAPTCHA = '//www.google.com/recaptcha/api.js?';
    const API_VERIFY_RECAPTCHA = 'https://www.google.com/recaptcha/api/siteverify';

    const TYPE_HCAPTCHA = 'H';
    const TYPE_RECAPTCHA = 'G';

    const TYPES_CAPTCHA = [
        self::TYPE_HCAPTCHA => 'hCaptcha',
        self::TYPE_RECAPTCHA => 'reCaptcha'
    ];

    public $pos = self::POS_CENTER;

    public $size;

    public $type;

    public $theme;

    public $siteKey;

    public $clientOptions = [];

    public function init()
    {
        parent::init();

        /** use validator config to capture key or|and type */
        if ((empty($this->siteKey) || empty($this->type)) && $this->hasModel()) {
            foreach ($this->model->activeValidators as $validator)
            {
                if (!($validator instanceof CaptchaValidator)) {
                    continue;
                }

                if (!in_array($this->attribute, $validator->attributes)) {
                    continue;
                }

                $this->type = empty($this->type)? $validator->type: $this->type;
                $this->siteKey = empty($this->siteKey)? $validator->siteKey: $this->siteKey;
            }
        }

        /** use default key when development environment is set */
        if (empty($this->siteKey) && defined('YII_ENV') && YII_ENV === 'dev') {
            $this->siteKey = $this->type === self::TYPE_RECAPTCHA?
                self::DEFAULT_RECAPTCHA_SITEKEY:
                self::DEFAULT_HCAPTCHA_SITEKEY;
        }

        /** div options */
        $this->clientOptions = array_merge([
            'id' => $this->getId().'_c',
            'class' => '',
            'data-size' => $this->size,
            'data-theme' => $this->theme,
            'data-sitekey' => $this->siteKey,
        ],
            $this->clientOptions
        );

        /** change position */
        $this->clientOptions['class'] = strtr("captcha-size captcha-{pos} captcha-no-print {class}", [
            '{class}' => $this->clientOptions['class'],
            '{pos}' => $this->pos,
        ]);
    
        /** validate widget config **/
        if (!in_array($this->type,  array_keys(Captcha::TYPES_CAPTCHA))) {
            throw new InvalidConfigException("Widget Captcha needs option 'type' must be Captcha::TYPE_HCAPTCHA or Captcha::TYPE_RECAPTCHA");
        }
        else if (empty($this->clientOptions['data-sitekey']) || !is_string($this->clientOptions['data-sitekey'])){
            throw new InvalidConfigException("Captcha missing 'siteKey' in production mode.");
        }
    }

    public function run()
    {
        CaptchaAsset::register($this->view);

        switch ($this->type) {
            case self::TYPE_RECAPTCHA:
                $this->view->registerJsFile(self::API_CLIENT_RECAPTCHA.http_build_query([
                    'onload' => 'dynamikacaptcha'.$this->getId(),
                    'render' => 'explicit',
                ]));
                $this->view->registerJs(strtr('function dynamikacaptcha{id}(){loadCaptcha(grecaptcha, "{id}_c", "#{input}", "{key}")}', [
                    '{input}' => $this->hasModel()? Html::getInputId($this->model, $this->attribute): $this->getId(),
                    '{key}' => $this->clientOptions['data-sitekey'],
                    '{id}' => $this->getId(),
                ]),
                    $this->view::POS_BEGIN
                );
                break;

            case self::TYPE_HCAPTCHA:
                $this->view->registerJsFile(self::API_CLIENT_HCAPTCHA.http_build_query([
                    'onload' => 'dynamikacaptcha'.$this->getId(),
                    'render' => 'explicit',
                ]));
                $this->view->registerJS(strtr('function dynamikacaptcha{id}(){loadCaptcha(hcaptcha, "{id}_c", "#{input}", "{key}")}', [
                    '{input}' => $this->hasModel()? Html::getInputId($this->model, $this->attribute): $this->getId(),
                    '{key}' => $this->clientOptions['data-sitekey'],
                    '{id}' => $this->getId(),
                ]),
                    $this->view::POS_BEGIN
                );
                break;
        }

        return $this->renderInputHtml('hidden').Html::tag('div', null, $this->clientOptions);
    }
}

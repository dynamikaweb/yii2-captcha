dynamikaweb/yii2-captcha
=========================
![php version](https://img.shields.io/packagist/php-v/dynamikaweb/yii2-captcha)
![pkg version](https://img.shields.io/packagist/v/dynamikaweb/yii2-captcha)
![license](https://img.shields.io/packagist/l/dynamikaweb/yii2-captcha)
![quality](https://img.shields.io/scrutinizer/quality/g/dynamikaweb/yii2-captcha)
![build](https://img.shields.io/scrutinizer/build/g/dynamikaweb/yii2-captcha)

Features
--------

 * Custom alignment
 * Centered by default
 * Not jQuery dependent
 * Not printable by default
 * Development keys by default
 * Better responsive Improvements
 * Support for google [reCaptcha](https://developers.google.com/recaptcha) and also [hCaptcha](https://www.hcaptcha.com)
 * Support for multiple validators, widgets and keys at the same time

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```SHELL
$ composer require dynamikaweb/yii2-captcha "*"
```

or add

```JSON
"dynamikaweb/yii2-captcha": "*"
```

to the `require` section of your `composer.json` file.

Usage
-----

### Widget using input name ###

* **View file**

```PHP
<?php 

use dynamikaweb\captcha\Captcha;
?>

<?=Captcha::widget([
  'siteKey' => 'XXX',
  'name' => 'captcha',
  'pos' => Captcha::POS_LEFT // or Captcha::POS_CENTER or Captcha::POS_RIGHT (optional)
  'size' => Captcha::SIZE_COMPACT // or Captcha::SIZE_NORMAL (optional)
  'type' => Captcha::TYPE_RECAPTCHA // or Captcha::TYPE_HCAPTCHA 
])?>
```

### Widget using model (site key in model file) ###

* **Model file**

```PHP
<?php 

namespace app\models;

use dynamikaweb\captcha\CaptchaValidator;

class SomeModel extends yii\base\Model
{
  public $captcha;

  public function rules()
  {
    return [
      [['captcha'], CaptchaValidator::classname(),
        'type' => CaptchaValidator::TYPE_RECAPTCHA, // or CaptchaValidator::TYPE_HCAPTCHA
        'siteKey' => 'XXX', // (optional)
        'secret' => 'XXX'
      ]
    ];
  }
}
```

* **View file**

```PHP
<?php 

use dynamikaweb\captcha\Captcha;
?>

<?=$form->attribute($model, 'captcha')->widget('dynamikaweb\captcha\Captcha')?>
```

### Widget using model (site key in view file) ###

* **Model file**

```PHP
<?php 

namespace app\models;

use dynamikaweb\captcha\CaptchaValidator;

class SomeModel extends yii\base\Model
{
  public $captcha;

  public function rules()
  {
    return [
      [['captcha'], CaptchaValidator::classname(),
        'type' => CaptchaValidator::TYPE_RECAPTCHA, // or CaptchaValidator::TYPE_HCAPTCHA
        'secret' => 'XXX'
      ]
    ];
  }
}
```

* **View file**

```PHP
<?php 

use dynamikaweb\captcha\Captcha;
?>

<?=$form->attribute($model, 'captcha')->widget('dynamikaweb\captcha\Captcha', [
  'siteKey' => 'XXX',
  'size' => Captcha::SIZE_COMPACT // or Captcha::SIZE_NORMAL (optional)
  'pos' => Captcha::POS_LEFT // or Captcha::POS_CENTER or Captcha::POS_RIGHT (optional)
])?>
```

--------------------------------------------------------------------------------------------------------------
[![dynamika soluções web](https://avatars.githubusercontent.com/dynamikaweb?size=12)](https://dynamika.com.br)
This project is under [BSD-3-Clause](https://opensource.org/licenses/BSD-3-Clause) license.

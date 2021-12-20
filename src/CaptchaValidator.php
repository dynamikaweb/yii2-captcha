<?php 

namespace dynamikaweb\captcha;

use Curl\Curl;
use yii\base\InvalidConfigException;
use yii\base\Exception;
use yii\helpers\Json;

class CaptchaValidator extends \yii\validators\Validator
{
    const TYPE_HCAPTCHA = Captcha::TYPE_HCAPTCHA;
    const TYPE_RECAPTCHA = Captcha::TYPE_RECAPTCHA;

    public $enableClientValidation = false;

    public $type;

    public $secret;

    public $siteKey;

    public function init()
    {
        parent::init();

        /** use default secret when development environment is set */
        if (empty($this->secret) && defined('YII_ENV') && YII_ENV === 'dev') {
            $this->secret = $this->type === Captcha::TYPE_RECAPTCHA?
                Captcha::DEFAULT_RECAPTCHA_SECRET:
                Captcha::DEFAULT_HCAPTCHA_SECRET;
        }

        /** validate rule config **/
        if (!in_array($this->type,  array_keys(Captcha::TYPES_CAPTCHA))) {
            throw new InvalidConfigException("Validator needs option 'type' must be CaptchaValidator::TYPE_HCAPTCHA or CaptchaValidator::TYPE_RECAPTCHA");
        }
        else if(empty($this->secret)) {
            throw new InvalidConfigException("Validator needs option 'secret' in production mode.");
        }
    }

    protected function validateValue($value)
    {
        try {
            $curl = new Curl();

            $curl->post($this->type === Captcha::TYPE_RECAPTCHA?
                Captcha::API_VERIFY_RECAPTCHA:
                Captcha::API_VERIFY_HCAPTCHA,
            [
                'secret' => $this->secret,
                'response' => $value
            ]);

            if (!$curl->isSuccess()) {
                throw new Exception("Invalid captcha.");
            }

            if (!Json::decode($curl->response)['success']) {
                throw new Exception("Invalid captcha.");
            }

            $curl->close();
        }
        catch (Exception $e) {
            return [$e->getMessage(), []];
        }
    }
}

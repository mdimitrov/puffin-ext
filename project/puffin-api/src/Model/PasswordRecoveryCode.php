<?php
/**
 * Created by PhpStorm.
 * User: mihael
 * Date: 2/12/17
 * Time: 19:47
 */

namespace Puffin\Model;


class PasswordRecoveryCode implements ModelInterface
{
    public $userId = null;

    public $code = '';

    public static function generateVerificationCode($userId)
    {
        return md5(uniqid($userId, true));
    }

    public function __construct($userId, $code = null)
    {
        if (!$code) {
            $code = PasswordRecoveryCode::generateVerificationCode($userId);
        }
        $this->userId = $userId;
        $this->code = $code;
    }

    public static function fromState(array $state)
    {
        return new self(
            $state['userId'],
            $state['code']
        );
    }

    public function toAssoc()
    {
        return [
            'userId' => $this->userId,
            'code' => $this->code
        ];
    }

    public function setFromAssoc(array $data)
    {
        if (isset($data['userId'])) {
            $this->userId = $data['userId'];
        }
        if (isset($data['code'])) {
            $this->code = $data['code'];
        }
    }
}
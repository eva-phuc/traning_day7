<?php
/**
 * クレジットカードバリデーション
 */
namespace Common;

class CreditCardValidate
{
    const AMERICAN_EXPRESS = 'American_Express';
    const JCB              = 'JCB';
    const MASTERCARD       = 'Mastercard';
    const VISA             = 'Visa';

    protected static $_card_length = array(
        self::AMERICAN_EXPRESS => array(15),
        self::JCB              => array(16),
        self::MASTERCARD       => array(16),
        self::VISA             => array(16),
    );

    protected static $_card_type = array(
        self::AMERICAN_EXPRESS => array('34', '37'),
        self::JCB              => array('3528', '3529', '353', '354', '355', '356', '357', '358'),
        self::MASTERCARD       => array('51', '52', '53', '54', '55'),
        self::VISA             => array('4'),
    );

    /**
     * カード番号の有効性チェック
     */
    public static function _validation_credit_card($val, $card_type=null)
    {
        if(is_null($card_type)) 
            $types = array_keys(static::$_card_length);
        else if(is_string($card_type))
            $types = array($card_type);
        else
            $types = $card_type;


        if(! is_string($val) || ! ctype_digit($val)) return false;

        //--- カード種類ごとのレングスとプレフィックスチェック
        $length = strlen($val);
        $foundp = false;
        $foundl = false;
        foreach ($types as $type) {
            foreach (static::$_card_type[$type] as $prefix) {
                if (substr($val, 0, strlen($prefix)) == $prefix) {
                    $foundp = true;
                    if (in_array($length, static::$_card_length[$type])) {
                        $foundl = true;
                        break 2;
                    }
                }
            }
        }

        if (false === $foundp || false === $foundl)
            return false;

        //--- デジットチェック
        $sum    = 0;
        $weight = 2;
        for ($i = $length - 2; $i >= 0; $i--) {
            $digit = $weight * $val[$i];
            $sum += floor($digit / 10) + $digit % 10;
            $weight = $weight % 2 + 1;
        }

        if ((10 - $sum % 10) % 10 != $val[$length - 1]) return false;

        return true;
    }

    /**
     * 有効期限の妥当性チェック
     */
    public static function _validation_expiration_date($val)
    {
        $checkval = $val;
        if(4 > strlen($checkval)) {
            $repeat_length  = intval(4-strlen($val)); 
            $checkval      .= str_repeat('0', $repeat_length);
        }
        (4 < strlen($checkval)) and list($checkval) = str_split($val, 4);

        list($checky,$checkm) = str_split($checkval, 2);
        if(! checkdate(intval($checkm), 1, intval('20'.$checky))) return false;

        return ($checkval > date("ym"));
    }

    /**
     * 文字数範囲
     */
    public static function _validation_strlen_between($val,$min=0,$max=1000)
    {
        $length = mb_strlen($val);
        return ($min <= $length && $max >= $length);
    }

}

<?php
namespace Ios;

/**
 * お問合せフォームのバリデーションチェック
 * 
 * @author higuchi
 */
class Model_Inquiry_Validate extends \Model {
    
    private $_val;
    
    
    /**
     * contract バリデータクラスインスタンス
     * 
     * @access public
     * @return array
     * @author higuchi
     */
    public function __construct() {
        $this->_val = \Validation::forge();
    }
    
    /**
     * 入力項目チェック
     * 
     * @access public
     * @return array
     * @author higuchi
     */
    public function inputCheck() {
        
        $this->_val->add_field('mail_addr', '「ご連絡先」', 'required|valid_email');
        $this->_val->add_field('body', '「お問い合わせ内容」', 'required');
        return $this->_val;
    }
}
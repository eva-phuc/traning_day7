<?php
namespace Ios;

/**
 * 選択データのバリデートチェック
 * 
 * @author higuchi
 */
class Model_Bookmark_Validate extends \Model {
    
    private $_itemData;
    private $_isCheck = true;
    
    /**
     * contract
     * 
     * @access public
     * @return array
     * @author higuchi
     */
    public function __construct($itemData) {
        $this->_itemData = $itemData;
    }
    
    
    /**
     * 各必須項目の入力チェック
     * 
     * @access public
     * @param array $inputData
     * @return array
     * @author higuchi
     */
    public function inDataCheck($inputData) {
        
        // アラート選択
        if (empty($inputData['alert'])) {
            return $this->_isCheck = false;
        }

        // オプション選択
        if (isset($this->_itemData['item']['options']) && is_array($this->_itemData['item']['options'])) {

            // オプション選択が空だとエラー
            if (empty($inputData['option'])) {
                return $this->_isCheck = false;
            }
            foreach ($inputData['option'] as $value) {
                if(empty($value) || $value ==0) return false;
            }
            
            // オプション数と選択数が相違したらエラー
            if (count($this->_itemData['item']['sub_options']) != count($inputData['option'])) {
                return $this->_isCheck = false;
            }
        }
        
        return $this->_isCheck;
    }
}
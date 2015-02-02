<?php
namespace Ios;

/**
 * ログイン
 * 
 * @author higuchi
 */
class Model_Common_Login extends \Model {


    /**
    * ログインチェックとユーザーデータ取得
    * 
    * @access public
    * @param array $params
    * @return array
    * @author higuchi
    */
    public static function check($params = array(), $redirect = true) {
        // ユーザの接続情報取得
        $disfa = \Input::param('_disfa');
        if(empty($disfa)) $disfa = \Input::cookie('_disfa'); 
        
        // 会員周りの情報取得メソッド呼び出し
        \Package::load('aucfan');
        $aucfanAuth = \Aucfan\Auth::forge($disfa);
        $is_login = $aucfanAuth->is_login();
        
        // ログインエラーの場合
        if ($is_login === false) {
            if ($redirect == true) {
                \Response::redirect('ios/error/home/');
            }
            return false;
        }
        
        // 指定した会員情報を返す
        $userParams['_disfa'] = $disfa;    // _disfaは必須で返す
        if(! empty($params)) {
            $userData = $aucfanAuth->get_user_info($params);
            foreach ($params as $value) {
                $userParams[$value] = array_shift($userData);
            }
        }
        
        return $userParams;
    }
}
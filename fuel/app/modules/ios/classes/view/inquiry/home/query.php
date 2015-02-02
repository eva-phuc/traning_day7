<?php
/**
 * お問合せフォーム
 * 
 * @author higuchi
 */

class View_Inquiry_Home_Query extends \ViewModel {


    /**
     * フォーム表示用パラメータ整形
     * 
     * @access public
     * @return Response
     * @author higuchi
     */
    public function view() {
        $inputParams = \Input::post();
        $list = \Config::get('cn.query_list');
        if (isset($inputParams['mail_addr'])) {
            $mail_addr = $inputParams['mail_addr'];
        } else {
            $mail_addr = $this->mail_addr;
        }
        if (isset($inputParams['query_param'])) {
            $query_param = $inputParams['query_param'];
        } else {
            $query_param = $this->query_param;
        }
        if(! empty($this->error_msg)) {
            $this->_view->set_global('error_msg', $this->error_msg);
        }
        $this->_view->set_global('mail_addr', $mail_addr);
        $this->_view->set_global('query_list', $list);
        $this->_view->set_global('query_param', $query_param);
        $this->_view->set_global('_disfa', $this->_disfa);
    }
}
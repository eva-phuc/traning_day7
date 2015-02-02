<?php

return array(
    'cookie' => array(
        'name'   => '_disfa',
        'expire' => (time()+60*60*24*14),
        'domain' => '.aucuniv.com',
        'path'   => '/',
    ),
    'url' => array(
        'login'  => 'http://192.168.2.161/aucfan_service_manager/alermo_login',
        'logout' => 'http://192.168.2.161/aucfan_service_manager/alermo_logout',
        'regist' => 'http://192.168.2.161/aucfan_service_manager/create_user',
        'get_user_profile' => 'http://192.168.2.161/aucfan_service_manager/get_user_profile',
        'mailaddr_change'  => 'http://192.168.2.161/aucfan_service_manager/change_mail_addr',
        'password_change'  => 'http://192.168.2.161/aucfan_service_manager/password_change',
        'password_check'   => 'http://192.168.2.161/aucfan_service_manager/check_password',
        'password_reminder'  => 'http://192.168.2.161/aucfan_service_manager/password_remind',
    ),
    'defaults' => array(
        'api' => array(
            'login' => array(
                'account_type'=>'mailaddr_or_loginid',
                'session_type'=>'alermo',
                'user_remote_addr'=>\Input::ip(),
                ),
            'logout' => array(
                'user_remote_addr'=>\Input::ip(),
                ),
            'password_change' => array(
                ),
            'password_check' => array(
                'account_type'=>'mailaddr_or_loginid',
                ),
        ),
        'json_templates' => array(
            'login' => array(
                'success' => array('status'=>'success', 'session_id'=>'','nickname'=>'','expire'=>'',),
                'error'   => array('status'=>'error','error_message'=>'ニックネームが取得出来ませんでした。'),
            ),
            'logout' => array(
                'success' => array('status'=>'success'),
                'error'   => array('status'=>'error','error_message'=>''),
            ),
            'regist' => array(
                'success' => array('status'=>'success', 'session_id'=>'','nickname'=>'','expire'=>'',),
                'error'   => array('status'=>'error','error_message'=>'登録に失敗しました。お手数ですが、再度入力をお願いします。'),
            ),
        ),
    ),
    'validate' => array(
        'login' => array(
            'required' => array('account_type','session_type','user_remote_addr','account','password',),
        ),
        'regist' => array(
            'check_keys' => array('nickname','mailaddr','mailaddr_cnf','password','password_cnf','info_mail_is_permitted'),
            'required'   => array('nickname'=>1,'mailaddr'=>1,'mailaddr_cnf'=>1,'password'=>1,'password_cnf'=>1,'info_mail_is_permitted'=>1),
            'compared'   => array('mailaddr_cnf'=>'mailaddr','password_cnf'=>'password'),
            'length'     => array(
                'nickname' =>array('min'=>2,'max'=>16),
                'mailaddr' => array('max'=>128),
                'mailaddr_cnf' => array('max'=>128),
                'password' => array('min'=>8,'max'=>16),
                'password_cnf' => array('min'=>8,'max'=>16)
            ),
            'email'      => array('mailaddr'=>1,'mailaddr_cnf'=>1),
            'regex'      => array(
                'password'     => '/^[0-9a-zA-Z\-_\.]+$/',
                'password_cnf' => '/^[0-9a-zA-Z\-_\.]+$/',
            ),
            'special'    => array('info_mail_is_permitted'=>'info_mail_is_permitted'),
        ),
        'password_change' => array(
            'required' => array('mailaddr','user_id','password','new_password','new_password_cnf',),
        ),
        'password_check' => array(
            'required' => array('account_type','account','password',),
        ),
        'mailaddr_change' => array(
            'required' => array(
                'normal'     => array('user_id','mailaddr','mailaddr_cnf'),
                'additional' => array('user_id','additional_mailaddr','additional_mailaddr_cnf','additional_mailaddr_type'),
                ),
            'compared' => array(
                'normal'     => array('mailaddr' => 'mailaddr_cnf'),
                'additional' => array('additional_mailaddr' => 'additional_mailaddr_cnf'),
                ),
        ),
        'password_reminder' => array(
            'required' => array('mailaddr',),
        ),
    ),
    'error_messages' => array(
        'invalid_system'    => 'システムエラーが発生しました。',
        'invalid_required' => ':labelは必須項目です。',
        'invalid_login'    => 'ログイン認証に失敗しました。',
        'invalid_logout'   => 'ログアウトに失敗しました。',
        'id_or_password_error' => '認証に失敗しました。',
        'invalid_match_value' => '確認用の:labelが一致しません。',
        'invalid_min_length'  => ':labelは:param:1文字以上で入力してください。',
        'invalid_max_length'  => ':labelは:param:1文字以下で入力してください。',
        'invalid_strlen_between'  => ':labelは:param:1文字以上:param:2文字以下で入力してください。',
        'invalid_match_pattern'   => ':labelに使用できない文字が含まれています。',
        'invalid_info_mail_is_permitted' => ':labelの設定が正しくありません。',
        'invalid_valid_email'     => 'メールアドレスの形式が間違っています。',
        'invalid_user_id'         => 'ユーザIDが取得できませんでした。',
        'invalid_password_check'  => 'パスワードが一致しません。',
        'invalid_mailaddr'        => 'メールアドレスの形式が間違っています。',
        'invalid_mailaddr_exists' => 'ご指定のメールアドレスは既に登録されています。',
        'invalid_mail_alert'  => 'メール通知設定エラーが発生しました。',
    ),
    'key_label' => array(
        'account_type'  => 'システム',
        'session_type'  => 'システム',
        'user_remote_addr'  => 'システム',
        'account'        => 'メールアドレス',
        'password'       => 'パスワード',
        'nickname'      => 'ニックネーム',
        'mailaddr'      => 'メールアドレス',
        'mailaddr_cnf'  => 'メールアドレス再入力',
        'password_cnf'  => 'パスワード再入力',
        'info_mail_is_permitted' => 'メール設定',
        'user_id'       => 'ユーザID',
        'new_password'     => '新しいパスワード',
        'new_password_cnf' => '新しいパスワード（再入力）',
    ),
    'mappings' => array(
        'user' => array(
            'パスワードを再設定する'   => '/member/user/edit_passwd',
            'メールアドレスを変更する' => '/member/user/edit_mailaddress',
            'premium_link'             => array(
                    ''          => array('label' => 'プレミアムサービスの登録' ,'href' => ''),
                    'AVAILABLE' => array('label' => 'プレミアムサービスの停止' ,'href' => ''),
                    'STOP'      => array('label' => 'プレミアムサービスの再開' ,'href' => ''),
                ),
        ),
        'alert_title' => array(
            'edit_passwd'      => '再設定に失敗しました',
            'edit_mailaddress' => '変更に失敗しました',
        ),
    ),
);

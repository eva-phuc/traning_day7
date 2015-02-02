#!/bin/sh
#
# title: YahooWallet側へ決済の差分チェック情報をアップロード
# author: shimma@aucfan.com
# version: v1.1 (2014/02/03 15:31:30)
# documentation: /Volumes/system_public/開発部フォルダ/オークファンシステム資料/02.開発関連/YahooAPI_Yconnect/認証API関連/wallet/YJ_Wallet_WC_SIG_YConnect.pdf
# dependencies:
#   - curl
#   - openssl
#   - nkf
# note:
#   - 基本的にプラグインが入っている場所であれば、どのサーバでも動きます
#   - 誤ってpostしてしまった場合などあれば、Yahoo側に連絡必要あり(FTPでログインしても削除権限がありません)
#   - YahooWallet決済の前月情報を当月3日の午前8時までにYahooのFTPへ転送する必要があります
#   - cron.d配下にあるファイルを/etc/cron.dに配置して下さい。
#
#########################################


BASEPATH=$(cd `dirname $0`; pwd)         # 現在の基準ディレクトリ
RESULT_DIR=${BASEPATH}/result            # 差分チェック結果保存ディレクトリ


main() {

    if [ ! -d $RESULT_DIR ]; then
        mkdir -p $RESULT_DIR
    fi

    log "INFO" "スクリプトを開始します"
        set -x
        target_month=${1:?差分チェック対象の月を第一引数に指定して下さい(current/last)} # 差分チェックAPIの取得月を指定します。(current/last)
        action=${2}                                                                     # 第二引数にsubmit_to_yahooを指定する場合、Yahoo側差分チェックデータをPostします
        temporary_file_path=yahoo-wallet-${target_month}-tmp.$$$$                       # データ取得一時ファイル名
        result_dir=$RESULT_DIR
        set +x


    log "INFO" "aucfanウォレット情報差分チェックデータ取得APIより結果データを取得します"
        set -x
        #sabuncheck_api_url=http://192.168.101.61/aucfan_service_manager/YConnect/get_sabun_check_data #ステージングAPI
        sabuncheck_api_url=http://192.168.2.61/aucfan_service_manager/YConnect/get_sabun_check_data #本番API
        curl -sS -d month=$target_month $sabuncheck_api_url -o $temporary_file_path || exit 1
        nkf -w -Lu --overwrite $temporary_file_path || exit 1
        set +x


    log "INFO" "Yahoo側の指定のファイル形式を作成します"
        set -x
        NNNN=1264                                                          # プロパティID (Yahooより発行されるコンテンツ識別ID)
        YYYYMMDDhhmmss=$(date "+%Y%m%d%H%M%S")                             # ファイル送信の日時
        ITEM_TYPE=MS                                                       # 商品タイプ(NU:買い切り/MS:月額)
        md5=$(openssl md5 ${temporary_file_path} | sed 's/^.* //')         # ハッシュ(ファイル自体のmd5値)
        sabuncheck_file_name=${NNNN}-${YYYYMMDDhhmmss}-${ITEM_TYPE}.${md5} # 仕様書p44を参照
        sabuncheck_file_path=${result_dir}/${sabuncheck_file_name}         # 生成データの保存先

        mv $temporary_file_path $sabuncheck_file_path || exit 1
        set +x


    log "INFO" "生成されたファイルをYahoo側へSubmitします: ${sabuncheck_file_name}"
        if [ "$action"x = "submit_to_yahoo"x ]; then
            set -x
            ftp_user=wg1264
            ftp_pass=wQjA3Ehh
            ftp_path=ftp://ftp.yahoofs.jp/check/
            curl -sS -T $sabuncheck_file_path -u $ftp_user:$ftp_pass $ftp_path || exit 1
            set +x
        else
            echo "- skipped"
        fi

    log "INFO" "スクリプトは正常に終了しました"

}

log() {
    now=$(date "+%Y-%m-%d %H:%M:%S")
    mode=$1
    msg=$2
    echo "${now} [${mode}]: ${msg}"
}

main "$@"

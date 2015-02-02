#!/bin/sh
#
# title: YahooWallet側へ決済の差分チェック情報をアップロード(手動)
# usage: ./create_sabun_check_data_manual.sh filepath [submit_to_yahoo]
# author: shimma@aucfan.com
# version: v1.1 (2014/02/03 15:31:30)
# documentation: /Volumes/system_public/開発部フォルダ/オークファンシステム資料/02.開発関連/YahooAPI_Yconnect/認証API関連/wallet/YJ_Wallet_WC_SIG_YConnect.pdf
# dependencies:
#   - curl
#   - openssl
# note:
#   - 基本的にプラグインが入っている場所であれば、どのサーバでも動きます
#   - 誤ってpostしてしまった場合などあれば、Yahoo側に連絡必要あり(FTPでログインしても削除権限がありません)
#   - YahooWallet決済の前月情報を当月3日の午前8時までにYahooのFTPへ転送する必要があります
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
        target_file_path=${1:?手動入稿対象のファイル名を指定してください}  # 手動で入稿する差分チェックファイルを指定します
        action=${2}                                                           # 第二引数にsubmit_to_yahooを指定する場合、Yahoo側差分チェックデータをPostします
        result_dir=$RESULT_DIR
        set +x


    log "INFO" "入稿対象ファイルをチェックします"
        if [ -f "$target_file_path" ]; then
            echo "- cheked"
        else
            echo "- ファイルが見つかりませんでした"
            exit 1
        fi


    log "INFO" "Yahoo側の指定のファイル形式を作成します"
        set -x
        NNNN=1264                                                          # プロパティID (Yahooより発行されるコンテンツ識別ID)
        YYYYMMDDhhmmss=$(date "+%Y%m%d%H%M%S")                             # ファイル送信の日時
        ITEM_TYPE=MS                                                       # 商品タイプ(NU:買い切り/MS:月額)
        md5=$(openssl md5 ${target_file_path} | sed 's/^.* //')         # ハッシュ(ファイル自体のmd5値)
        sabuncheck_file_name=${NNNN}-${YYYYMMDDhhmmss}-${ITEM_TYPE}.${md5} # 仕様書p44を参照
        sabuncheck_file_path=${result_dir}/${sabuncheck_file_name}         # 生成データの保存先

        cp $target_file_path $sabuncheck_file_path || exit 1
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

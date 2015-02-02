#!/bin/bash
#
# Tools Setup Scripts
#
#########################################


main() {

    #Definition
    #readonly BASEPATH=$(cd `dirname $0`; pwd)
    readonly BASEPATH=/deploy/tools

    if [ ! -d $BASEPATH ]; then
        echo "File doen't exist: $BASEPATH" 2>&1
        exit 1
    fi

    #Install REDIS (Require Epel Repo)
    yum -y install redis
    service redis start
    chkconfig redis on

    #Set Cache Directory
    chmod 777 -R ${BASEPATH}/fuel/app/cache
    chmod 777 -R ${BASEPATH}/fuel/app/logs
    chmod 777 -R ${BASEPATH}/fuel/app/config
    chmod 777 -R ${BASEPATH}/fuel/app/tmp
    chmod 777 -R ${BASEPATH}/fuel/app/modules/*/logs
    chmod 777 -R ${BASEPATH}/fuel/app/modules/*/cache
    chmod 777 -R ${BASEPATH}/public/upfiles

}

main "$@"


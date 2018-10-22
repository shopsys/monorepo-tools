#!/bin/sh -ex

# used to copy logs of codeception which are not streamed
WEBSERVER_PHP_FPM_CONTAINER_NAME="webserver-php-fpm"

for POD_NAME in $(kubectl get pods -n ${JOB_NAME} | grep -v ^NAME | cut -f 1 -d ' '); do
    # create directory for log files if it does not exist
    mkdir -p ${WORKSPACE}/logs/
    # remove already existing log file
    rm -f ${WORKSPACE}/logs/${POD_NAME}.log || true
    # print log output to log file
    kubectl logs ${POD_NAME} -n ${JOB_NAME} --all-containers > ${WORKSPACE}/logs/${POD_NAME}.log

    # check if pod name is php-fpm pod because of codeception logs
    if [[ ${POD_NAME} == *${WEBSERVER_PHP_FPM_CONTAINER_NAME}* ]]; then
        # copy codeception logs from php-fpm pod to local
        # we do not need to specify the php-fpm container because it is picked by default
        kubectl cp ${JOB_NAME}/${POD_NAME}:/var/www/html/project-base/var/logs/codeception/ ${WORKSPACE}/logs/codeception
    fi
done

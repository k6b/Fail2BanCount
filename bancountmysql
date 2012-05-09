#!/bin/bash

action=$1
ip=$2
port=$3

mysqldb=""
mysqlpw=""
mysqluser=""

geoip () {
	geoiplookup $ip | awk -F, '{print $2}' | sed s/\ //
}

case $action in

ban)
	mysql $mysqldb -u $mysqluser --password=$mysqlpw -e "INSERT INTO bans (ip, ban_date, ban_time, country) VALUES ( '$ip', '`date +%F`', '`date +%T`', '`geoip $ip`');"

;;

unban)
	mysql $mysqldb -u $mysqluser --password=$mysqlpw -e "INSERT INTO unbans (ip, unban_date, unban_time, country) VALUES ( '$ip', '`date +%F`', '`date +%T`', '`geoip $ip`');"
;;

*)
	cat << EOF
Fail2BanCount - by k6b - MySQL logger

$(basename $0) <ban/unban> <ip>

Performs geoip lookup and stamps time
and date into MySQL database.
EOF

;;

esac

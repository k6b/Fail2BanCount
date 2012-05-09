#! /bin/sh
# script to find the number of IPs banned by fail2ban
# by k6b kyle@kyleberry.org

# MySQL User/Database information

mysqldb=""
mysqlpw=""
mysqluser=""

#####
# First, we'll define a few functions so we don't have to
# do the same thing over and over again.
#####

ipfind () {
	mysql $mysqldb -u $mysqluser --password=$mysqlpw -e "SELECT ip,COUNT(*) count,country FROM bans GROUP BY ip HAVING count > 1 ORDER BY count DESC;" | sed '/ip/d'
}

recent () {
#	mysql $mysqldb -u $mysqluser --password=$mysqlpw -e "SELECT ip,ban_date,ban_time,country FROM bans WHERE id > ((SELECT MAX(id) FROM bans) - $total)" | sed '/ip/d'
	mysql $mysqldb -u $mysqluser --password=$mysqlpw -e "SELECT ip,ban_date,ban_time,country FROM bans WHERE bans.id NOT IN ( SELECT unbans.id FROM unbans WHERE bans.id=unbans.id)" | sed '/ip/d'
}

#####
# Now we'll define some global variables
#####

# Find the number of IPs banned
# Added sanity check for systems with no IPs banned

bans=$(mysql $mysqldb -u $mysqluser --password=$mysqlpw -e "SELECT MAX(id) from bans" | egrep "[0-9]")
unbans=$(mysql $mysqldb -u $mysqluser --password=$mysqlpw -e "SELECT MAX(id) FROM unbans" | egrep "[0-9]")

# Here we find the number of IPs currently banned by using the
# number we found earlier and subtracting it from the number of
# Unbans reported by fail2ban. I'm sure there's a better way to
# find this number.
# Added sanity check for systems with no IPs banned.

total=$(($bans - $unbans))

#####
# Begin the script
#####

# Print some text

echo -e '\n'"\033[4m\033[1mFail2BanCount - by k6b\033[0m"'\n'

# Use proper grammer :)

if [[ $bans -eq 0 ]]
then
	echo -e No IPs have been banned.
elif [[ $bans -ne 1 ]]
then
	echo -e $bans IPs have been banned.
else
	echo -e $bans IP has been banned.
fi

# Use the list of IPs we found to generate a list of IP's that
# have been banned more than once, along with the number of times
# it's been banned and it's country of origin.

echo -e '\n'"\033[4mIP\t\tBans\tCountry\033[0m"
ipfind

# We want to print the number of IPs that are currently banned,
# but we should use proper grammar. (Because why not?)

if [[ $total -ne "1" ]]
then
	echo -e '\n'Currently $total IPs are banned.'\n'
else
	echo -e '\n'Currently $total IP is banned.'\n'
fi

# If we have an IP currently banned, let's make another list showing
# the IP, the date and time of it's ban, and it's country of origin.

if [[ $total -ne "0" ]]
then

	# Print some more text

	echo -e "\033[4mCurrently Banned\033[0m"'\n'
	echo -e "\033[4mIP\t\tDate\t\tTime\t\tCountry\033[0m"
	
	recent

echo
fi

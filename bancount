#!/usr/local/bin/perl

use strict;
use warnings;
use DBI;
use DBD::mysql;
use Term::ANSIColor;

my $mysqldb = "fail2bancount";
my $mysqlpw = "OJ9tzckQyVqCLzut";
my $mysqluser = "fail2bancount";

my $db = DBI->connect(
	"DBI:mysql:database=$mysqldb",
	"$mysqluser",
	"$mysqlpw"
	) || die "Cannot connect to database: $!\n";

my $currentbans = $db->prepare("SELECT ip,ban_date,ban_time,country FROM bans WHERE bans.id NOT IN ( SELECT unbans.id FROM unbans WHERE bans.id=unbans.id)");
my $bans = $db->prepare("SELECT MAX(id) FROM bans");
my $unbans = $db->prepare("SELECT MAX(id) From unbans");
my $multiplebans = $db->prepare("SELECT ip,COUNT(*) count,country FROM bans GROUP BY ip HAVING count > 1 ORDER BY count DESC");

$currentbans->execute();
$bans->execute();
$unbans->execute();
$multiplebans->execute();

# find total bans

while (my $ref = $bans->fetchrow_arrayref) {
	our $totalban = $ref->[0];
	}

while (my $ref = $unbans->fetchrow_arrayref) {
	our $totalunban = $ref->[0];
	}

our ($totalban,$totalunban);
our $currentlybanned = $totalban - $totalunban;

print color 'bold';
print color 'underline';
print "\nFail2BanCount - by k6b\n\n";
print color 'reset';

print "$totalban IPs have been banned.\n\n";

# find IPs banned more than once

print color 'underline';
print "IP\t\tBans\tCountry\n";
print color 'reset';

while (my $ref = $multiplebans->fetchrow_arrayref) {
	print $ref->[0] . "\t" . $ref->[1] . "\t" . $ref->[2] . "\n";
	}

print "\n";

if ($currentlybanned == 1) {
	print "Currently $currentlybanned IP is banned.\n\n";
} else {
	print "Currently $currentlybanned IPs are banned.\n\n";
	}

# Print current bans

if ($currentlybanned > 0) {
print color 'underline';
print "Currently Banned\n\n";

print "IP\t\tDate\t\tTime\t\tCountry\n";
print color 'reset';

while (my $banrow = $currentbans->fetchrow_arrayref) {
	print $banrow->[0] . "\t" . $banrow->[1] . "\t" . $banrow->[2] . "\t" . $banrow->[3] . "\n";
	}

print "\n";
	}

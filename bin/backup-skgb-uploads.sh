#! /bin/bash
# $Id$

# /etc/cron.daily/backup-skgb-uploads: weekly backup script for SKGB uploaded files

# Start in the root filesystem, make SElinux happy
cd /
bak=/var/backups
LOCKFILE=/var/lock/cron.daily
umask 022

#
# Avoid running more than one at a time 
#

if [ -x /usr/bin/lockfile-create ] ; then
    lockfile-create $LOCKFILE
    if [ $? -ne 0 ] ; then
	cat <<EOF

Unable to run /etc/cron.daily/backup-skgb because lockfile $LOCKFILE
acquisition failed. This probably means that the previous day's
instance is still running. Please check and correct if necessary.

EOF
	exit 1
    fi

    # Keep lockfile fresh
    lockfile-touch $LOCKFILE &
    LOCKTOUCHPID="$!"
fi

#
# Backup key system data
#

uploadsdir=/srv/skgb.www/uploads
backupfile=skgb_uploads.tar.bz2

if cd $bak ; then
	tar -c ${uploadsdir}/* | bzip2 -c > "$backupfile"
fi

#
# Clean up lockfile
#
if [ -x /usr/bin/lockfile-create ] ; then
    kill $LOCKTOUCHPID
    lockfile-remove $LOCKFILE
fi

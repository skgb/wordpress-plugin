#! /bin/bash
# $Id$


# try to parse verbose paramter

if [[ "$2" == "--verbose" || "$2" == "-v" ]] ; then
	VERBOSE="-v"
elif [[ "$2" == "--changes" || "$2" == "-c" || "$2" == "" ]] ; then
	VERBOSE="-c"
elif [[ "$2" == "--quiet" || "$2" == "-q" ]] ; then
	VERBOSE=""
elif [[ "$2" == "--silent" || "$2" == "-f" ]] ; then
	VERBOSE="-f"
else
	VERBOSE="ERROR"
fi

# ask for confirmation unless the user uses -y

if [[ $# -lt 1 || $# -gt 2 || $VERBOSE == "ERROR" || "$1" != "-y" ]] ; then
	echo "Usage: `basename $0` -y [-vcqf]"
	echo "  -y --yes      'Yes, I've read these instructions.'"
	echo "  -v --verbose  output a diagnostic for every file processed"
	echo "  -c --changes  like verbose but report only when a change is made (default)"
	echo "  -q --quiet    only report errors"
	echo "  -f --silent   suppress most error messages"
	echo "  -? --help     display this help and exit"
	echo "Only one of the [-vcqf] options can be used at a time."
	printf "\n*****  WARNING  *****\n\n"
	echo "Running this script will change file system flags of everything inside the"
	echo "current directory (`pwd`). This action is not undo-able."
	echo "As a safety measure, this script will not run unless you use the -y option."
	exit 1
fi


# defaults

if [[ $VERBOSE == "-v" ]] ; then printf "\nSetting default owner and group...\n" ; fi
sudo chown $VERBOSE --no-dereference -R www-run:www-data .

if [[ $VERBOSE == "-v" ]] ; then printf "\nSetting default permissions...\n" ; fi
sudo chmod $VERBOSE -R u+rwX,g+rwX,o-rwx .	


# special flags

if [[ $VERBOSE == "-v" ]] ; then printf "\nSetting special owner for customised plug-ins...\n" ; fi
sudo chown $VERBOSE --no-dereference -R "`whoami`:www-data" \
	extensions/plugins/ajax-comment-preview \
	extensions/plugins/bad-behavior \
	extensions/plugins/bhcalendarchives \
	extensions/plugins/remove-generator-meta-tag.php

if [[ $VERBOSE == "-v" ]] ; then printf "\nSetting special owner and group for SKGB software...\n" ; fi
sudo chown $VERBOSE --no-dereference -R "`whoami`:skgb-web" \
	XML \
	extensions/plugins/skgb-web \
	extensions/plugins/secure-comment.php \
	extensions/themes/skgb4 \
	extensions/themes/skgb5

if [[ $VERBOSE == "-v" ]] ; then printf "\nSetting special permissions for helper script files...\n" ; fi
sudo chmod $VERBOSE 0770 \
	extensions/plugins/skgb-web/bin/install_new.sh \
	extensions/plugins/skgb-web/bin/repair_permissions.sh

if [[ $VERBOSE == "-v" ]] ; then printf "\nSetting special permissions for config files...\n" ; fi
sudo chmod $VERBOSE 0460 .htaccess htaccess*.conf wp-config*.php index.php

if [[ $VERBOSE == "-v" ]] ; then printf "\nSetting special permissions for upgrade temp directory...\n" ; fi
sudo chmod $VERBOSE 2750 extensions/upgrade

if [[ $VERBOSE == "-v" ]] ; then printf "\nSetting special permissions for uploads directory...\n" ; fi
sudo chmod $VERBOSE 2770 uploads uploads/* uploads/*/*
sudo chmod $VERBOSE 0660 uploads/*/*/*

if [[ $VERBOSE == "-v" ]] ; then printf "\nSetting special flags for archive files...\n" ; fi
sudo chown $VERBOSE "`whoami`:`whoami`" archive.tar archive.tar.gz
sudo chmod $VERBOSE 0600 archive.tar archive.tar.gz


if [[ $VERBOSE == "-v" ]] ; then printf "\nDone.\n" ; fi

exit 0

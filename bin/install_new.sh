#! /bin/bash
# $Id$

#WORDPRESS_DEFAULT="http://wordpress.org/latest.tar.gz"  # tay only runs PHP 5.2.0, so WP updates stop at 3.1.4
WORDPRESS_DEFAULT="http://wordpress.org/wordpress-3.1.4.tar.gz"
PLUGINREP_DEFAULT="http://downloads.wordpress.org/plugin/"

if [[ $# -lt 1 || $# -gt 4 || ($1 != "www" && $1 != "dev") ]] ; then
	echo "Usage: `basename $0` host [uploads [wordpress [pluginrep]]]"
	echo "  host: 'www' or 'dev'"
	echo "  uploads: path to gzip'ed tar file with contents of the uploads directory"
	echo "    (leave off to create an empty uploads directory)"
	echo "  wordpress: URI of gzip'ed tar file with the wordpress directory"
	echo "    (leave off to use default '$WORDPRESS_DEFAULT')"
	echo "  pluginrep: URI of directory with zip'ed plugin archives"
	echo "    (leave off to use default '$PLUGINREP_DEFAULT')"
	printf "\n*****  WARNING  *****\n\n"
	echo "Running this script will completely replace all contents of the current"
	echo "directory (`pwd`). This action is not undo-able."
	exit 1
fi

HOST="$1"
UPLOADS="$2"
WORDPRESS="$3"
PLUGINREP="$4"


printf "\n\nClearing directory...\n"

rm -v "archive.tar" "archive.tar.gz"
SCRIPTNAME=`basename "$0" '.sh'`
TARFILE=`mktemp -t "${scriptname}.XXXXXX"`
sudo tar -cf "$TARFILE" .???* *
sudo rm -vRf .???*
echo "Redirect 503 /index.php" > .htaccess
echo "Temp 503 HTTP status enabled."
sudo rm -vRf *
sudo mv "$TARFILE" "archive.tar"
sudo gzip "archive.tar" &


printf "\nExporting skgb/skgb-web/trunk HEAD from Subversion...\n"

svn export "file:///usr/local/svnreps/skgb/skgb-web/trunk" . --force --native-eol LF
mkdir --mode=2750 -p extensions/upgrade
echo "ErrorDocument 503 /extensions/themes/skgb5/error/503.php" >> .htaccess
echo "Temp 503 HTTP status error page changed to link to skgb5 theme."


printf "\nExporting open/wp-plugins/... HEAD from Subversion...\n"

svn export "file:///usr/local/svnreps/open/wp-plugins/ajax-comment-preview/trunk" extensions/plugins --force --native-eol LF
svn export "file:///usr/local/svnreps/open/wp-plugins/bad-behavior/trunk" extensions/plugins --force --native-eol LF
svn export "file:///usr/local/svnreps/open/wp-plugins/bhcalendarchives/trunk" extensions/plugins --force --native-eol LF
svn export "file:///usr/local/svnreps/open/wp-plugins/remove-generator-meta-tag/trunk" extensions/plugins --force --native-eol LF
svn export "file:///usr/local/svnreps/open/wp-plugins/search-meter/trunk" extensions/plugins --force --native-eol LF


printf "\nInstalling other Plug-ins...\n"

if [[ "$PLUGINREP" == "" ]] ; then
	PLUGINREP="$PLUGINREP_DEFAULT"
fi

get_plugin()
{
	curl "$2$1" -o "extensions/plugins/$1"
	unzip -d extensions/plugins "extensions/plugins/$1"
	rm -f "extensions/plugins/$1"
}

get_plugin "pjw-page-excerpt.0.02.zip" "$PLUGINREP"
#get_plugin "raw-html.1.3.zip" "$PLUGINREP"  # we now use markdown instead, which allows much the same
get_plugin "seemore.1.1.zip" "$PLUGINREP"
get_plugin "wp-db-backup.2.2.3.zip" "$PLUGINREP"

# leave the caching plugin out, just download the archive for manual installation
#curl "${PLUGINREP}w3-total-cache.0.9.1.3.zip" -o "extensions/plugins/w3-total-cache.0.9.1.3.zip"

get_plugin 'php-markdown-extra-1.2.4.zip' 'http://michelf.com/docs/projets/'
mv 'extensions/plugins/PHP Markdown Extra 1.2.4/markdown.php' extensions/plugins
rm -Rf 'extensions/plugins/PHP Markdown Extra 1.2.4'


printf "\nInstalling Wordpress...\n"

if [[ "$WORDPRESS" == "" ]] ; then
	WORDPRESS="$WORDPRESS_DEFAULT"
fi
curl "$WORDPRESS" | gunzip | tar -vx


printf "\nPreparing 'uploads' directory...\n"

mkdir --mode=2770 uploads
if [[ "$UPLOADS" != "" ]] ; then
	sudo chown www-run:www-data uploads
	gunzip -c "$UPLOADS" | tar -vx --directory uploads
fi
sudo chown --no-dereference -R www-run:www-data uploads


printf "\nLinking...\n"


rm -f .htaccess
ln -vs "htaccess_${HOST}.conf" .htaccess
ln -vs "wp-config_${HOST}.php" wp-config.php

ln -vs extensions wp-content
ln -vs '../uploads' extensions/uploads
ln -vs '../wordpress/wp-content/languages' extensions/languages


printf "\nFix File System Flags...\n"

"`dirname $0`/repair_permissions.sh" -y -q


printf "\nDone.\n"

echo "NB: The W3 Total Cache plug-in was left for manual installation:"
echo "<http://wordpress.org/extend/plugins/w3-total-cache/installation/>"

exit 0

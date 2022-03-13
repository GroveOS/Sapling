echo "Sapling requires CouchCMS be installed before its installation. Is Couch installed already? [y/n]"
read couch_installed
if [[ $couch_installed == 'y' ]]; then
	continue
else
	echo "Please install Couch first. You can come back here when you've done that."
	exit 1
fi

echo "Please type the location of your Couch directory. You can type 'default' or the directory location relative to your project root. No trailing slashes please."
read couch_dir
echo "Please type your Couch snippets directory. You can type 'default' or the directory location relative to your project root. No trailing slashes please."
read snippets_dir
echo "Sapling keeps quick commands handy in a directory in the root of your project. By default, this directory is 'sc', which enables you to call quick commands from your terminal like 'sc/create template posts'."
echo "What would you like to name this directory? You can type 'default' or your desired directory location relative to your project root. No trailing slashes please."
read sapling_commands_dir

# Set up sapling directory and config.sh file
mkdir $sapling_commands_dir
echo "couch_dir=$couch_dir" >> $sapling_commands_dir/config.sh
echo "snippets_dir=$snippets_dir" >> $sapling_commands_dir/config.sh
echo "sapling_commands_dir=$sapling_commands_dir" >> $sapling_commands_dir/config.sh

if [[ -d $couch_dir && -d $snippets_dir && $sapling_commands_dir ]]; then
	echo "Sapling installing . . ."
else
	echo "Something went wrong with your directory setup."
	exit 0
fi

cp -r Sapling/embed ./
cp -r Sapling/sc ./
for item in head header footer; do
	if [[ ! -f $snippets_dir/sapling/layout/$item.html ]]; then
		mv $snippets_dir/sapling/layout/sapling.$item.html $snippets_dir/sapling/layout/$item.html
	else
		mv $snippets_dir/sapling/layout/sapling.$item.html $snippets_dir/sapling/layout/$item.updated.html
	fi
done
rm -rf Sapling

echo "Sapling installed."
 #!/bin/bash

# Prepare installation based on arguments
if [ $1 == 'from-scratch' ]; then
	# Clone CouchCMS
	git clone https://github.com/CouchCMS/CouchCMS
	mv CouchCMS/couch ./
	rm -rf CouchCMS
	# Set up default snippets directory
	mkdir embed
	# Establish default variables
	couch_dir="couch"
	snippets_dir="embed"
	sapling_commands_dir="sc"
elif [ $1 == 'alongside-couch' ]; then
	if [ $3 == 'default' ]; then
		# Establish default variables alongside existing couch
		couch_dir=$2
		snippets_dir="embed"
		sapling_commands_dir="sc"
	else
		# Establish custom variables
		couch_dir=$2
		snippets_dir=$3
		sapling_commands_dir=$4
	fi
else
	couch_dir=$1
	snippets_dir=$2
	sapling_commands_dir=$3
fi




##### CHECK FOR VARIABLE ERRORS ######
# Check that arguments were given
if [ ! $couch_dir || ! $snippets_dir || ! $sapling_commands_dir ]; then
	echo "Missing arguments, try again."
	exit 0
fi
# Check that Couch and Snippets directories exists
if [ -d $couch_dir && -d $snippets_dir ]; then
	echo "Sapling installing . . ."
else
	echo "Something went wrong with your directory setup."
	exit 0
fi




# Set up sapling directory and prepare config.sh file
mkdir $sapling_commands_dir
echo "couch_dir=\"$couch_dir\"\nsnippets_dir=\"$snippets_dir\"\nsapling_commands_dir=\"$sapling_commands_dir\"" >> $sapling_commands_dir/config.sh






##### INSTALLTION #####

cp -r Sapling/embed/ ./$snippets_dir
cp -r Sapling/sc/ ./$sapling_commands_dir

# sapling/config.json
if [ ! -f $snippets_dir/sapling/config.json ]; then
	mv $snippets_dir/sapling/config.example.json $snippets_dir/sapling/config.json
fi

# sapling/addons/kfunctions.php
if [ ! -f $snippets_dir/sapling/addons/kfunctions.php ]; then
	mv $snippets_dir/sapling/addons/kfunctions.example.php $snippets_dir/sapling/addons/kfunctions.php
fi

# sapling/layout/ head header footer
for item in head header footer; do
	touch $snippets_dir/sapling/layout/$item.html
done

if [ $1 == 'from-scratch' ]; then
	# Install default Sapling flavored Couch configs 
	cp -r $snippets_dir/sapling/lib/couch/* $couch_dir/
	sc/create.sh template index
fi

# Clean up
rm -rf Sapling
echo "Sapling installed."
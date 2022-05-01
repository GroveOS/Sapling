#!/bin/bash

# How-to
# sapling/install.sh {:couch_directory} {:embed_directory} {:sc_directory}

# Setup default values
install_type='full';
couch_dir='couch';
snippets_dir='embed'
sc_dir='sc'

if [[ $# == 3 ]]; then
	install_type='custom';
	couch_dir=$1
	snippets_dir=$2
	sc_dir=$3
elif [[ $# == 4 ]]; then
	if [[ $1 == 'alongside' ]]; then
		couch_dir=$2;
		snippets_dir=$3;
		sc_dir=$4;
	else
		echo "Invalide argument. First argument must be 'alongside' if you are using four arguments --> bash sapling/install.sh alongside {:couch_dir} {:snippets_dir} {:sapling_commands_dir}."
		echo "Or, you can provide three arguments --> bash sapling/install.sh {:couch_dir} {:snippets_dir} {:sapling_commands_dir}; that will let you define those three directory names."
		echo "Or, you can provide no arguments --> bash sapling/isntall.sh; that will use default Sapling-flavored values --> bash sapling/install.sh couch embed sc"
	fi
elif [[ $# > 0 ]]; then
	echo "Invalid number of arguments. If you provide arguments, you must provide three --> bash sapling/install.sh {:couch_dir} {:embed_dir} {:sapling_commands_dir}."
	echo "Or, you can provide no arguments; that will install a clean installation of both CouchCMS and Sapling alongside.";
fi


# Install CouchCMS and setup snippets_dir. This is a clean install.
if [[ $install_type == 'full' ]]; then
	# Clone CouchCMS
	git clone https://github.com/CouchCMS/CouchCMS
	mv CouchCMS/couch ./
	rm -rf CouchCMS
	# Setup snippets directory
	mkdir $snippets_dir
fi






##### CHECK FOR VARIABLE ERRORS ######
# Check that couch_dir and snippets_dir directories do indeed exist
if [[ $install_type == 'custom' ]]; then
	if [[ ! -d $couch_dir || ! -d $snippets_dir ]]; then
		echo "Either your couch_dir or your snippets_dir is invalid. Make sure those directories exist if you want to do a custom set up.";
		exit 0
	fi
fi




# Set up sapling directory and prepare config.sh file
mkdir $sc_dir
echo "couch_dir=\"$couch_dir\"" >> $sc_dir/config.sh
echo "snippets_dir=\"$snippets_dir\"" >>  $sc_dir/config.sh
echo  "sc_dir=\"$sc_dir\"" >> $sc_dir/config.sh






##### INSTALL #####

cp -r Sapling/embed/* ./$snippets_dir/
cp -r Sapling/sc/* ./$sc_dir/
touch ./$snippets_dir/sapling.html

# sapling/config.json
if [[ ! -f $snippets_dir/sapling/config.json ]]; then
	mv $snippets_dir/sapling/config.example.json $snippets_dir/sapling/config.json
fi

# sapling/addons/kfunctions.php
if [[ ! -f $snippets_dir/sapling/addons/kfunctions.php ]]; then
	mv $snippets_dir/sapling/addons/kfunctions.example.php $snippets_dir/sapling/addons/kfunctions.php
fi

# sapling/layout/ head header footer
for item in head header footer; do
	touch $snippets_dir/sapling/layout/$item.html
done

if [[ $install_type == 'full' ]]; then
	# Install default Sapling flavored Couch configs 
	cp -r $snippets_dir/sapling/lib/couch/* $couch_dir/
	sc/create.sh template index
fi




# Clean up
rm -rf Sapling
echo "Sapling installed."

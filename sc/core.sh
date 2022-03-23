#!/bin/bash

##### - - - - - CONTENTS - - - - - #####

# - - - Check for errors before starting
# - - - Vars
# - - - Basic functions
# - - - Create functions
# - - - Remove functions



# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


##### - - - - - CHECK FOR ERRORS - - - - - #####

sapling_dir=$(dirname $0)

if [[ -f  $sapling_dir/config.sh ]]; then
	. $sapling_dir/config.sh
else
	echo "Warning: $(dirname $0)/config.sh missing. Using default values."
	couch_dir="couch"
	snippets_dir="embed"
	sapling_commands_dir="sc"
fi

# Check that config.sh values are valid
if [[ -d $sapling_dir/../$couch_dir && -d $sapling_dir/../$snippets_dir && -d $sapling_dir/../$sapling_commands_dir  ]]; then
	echo "Notice: default directories exist."
else
	if [[ ! -d $sapling_dir/../$couch_dir  ]]; then
		echo "Error: config value for Couch directory does not exist"
	fi
	if [[ ! -d $sapling_dir/../$snippets_dir  ]]; then
		echo "Error: config value for Couch snippets directory does not exist"
	fi
	if [[ ! -d $sapling_dir/../$sapling_commands_dir  ]]; then
		echo "Error: config value for Sapling commands directory does not exist"
	fi
fi



# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


##### - - - - - VARS - - - - - #####

root_dir=$(dirname $0)/..



# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


##### - - - - - BASIC FUNCTIONS - - - - - #####

# Throw function error
function throw_expectation_error {
	echo "Function $0 $desc"
}

# Throw funciton success
function throw_success {
	echo $success
}



# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -



##### - - - - - CREATE FUNCTIONS - - - - - #####

# Create a template file, given name
function create_template {
	desc="expects one argument: template_name"
	success="Successfully created template, $1.php"
	if [[ $1 ]]; then
		template=$1
		touch $root_dir/$template.php
		echo "<?php require_once('$couch_dir/cms.php');?>\n\n\n\n<?php COUCH::invoke();?>" >> $root_dir/$template.php
		throw_success
	else
		throw_expectation_error
	fi
}

# Create a template folder, given template
function create_template_folder {
	
	desc="expects one argument: template_name"
	success="Successfully made $1 template folder"

	if [[ $1 ]]; then

		# Create directories
		mkdir $root_dir/$snippets_dir/sapling/templates/$1
		mkdir $root_dir/$snippets_dir/sapling/templates/$1/views

		# Create files
		touch $root_dir/$snippets_dir/sapling/templates/$1/template.css
		for item in list show archive folder; do
			touch $root_dir/$snippets_dir/sapling/templates/$1/views/$item.html
			touch $root_dir/$snippets_dir/sapling/templates/$1/views/$item.css
		done
		for item in routes config-list config-form editables; do
			touch $root_dir/$snippets_dir/sapling/templates/$1/$item.html
		done

		if [[ -d $root_dir/$snippets_dir/sapling/templates/$1 && -d $root_dir/$snippets_dir/sapling/templates/$1/views ]]; then
			throw_success
		fi

	else
		throw_expectation_error
	fi
}



# - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -


##### - - - - - REMOVE FUNCTIONS - - - - - #####

# Remove template
function remove_template {
	desc="expects one argument: template_name"
	success="Successfully deleted $1.php"
	if [[ $1 ]]; then
		if [[ -f $root_dir/$1.php ]]; then
			rm $root_dir/$1.php
			throw_success
		else
			echo "$1 template does not exist"
		fi
	else
		throw_expectation_error
	fi
}


# Remove template folder
function remove_template_folder {
	desc="expects one argument: template_name"
	success="Successfully removed $1"
	if [[ $1 ]]; then
		if [[ -d $snippets_dir/sapling/templates/$1 ]]; then
			rm -r $snippets_dir/sapling/templates/$1
			throw_success
		else
			echo "$1 template directory does not exist"
		fi
	else
		throw_expectation_error
	fi
}


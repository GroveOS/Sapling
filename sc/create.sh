 #!/bin/bash

. $(dirname "$0")/core.sh


######### CREATE TEMPLATE #########
if [[ $1 == 'template' ]]; then
	template=$2
	# Create a template file, if it does not exist.
	if [[ ! -f $template.php ]]; then
		create_template $template
	else
		echo "Notice: you already have a $template.php file, so we didn't touch that."
	fi
	# Create a template folder, if it does not exist.
	if [[ ! -d $snippets_dir/templates/$template ]]; then
		create_template_folder $template
	else
		echo "Notice: your $template.php template folder already exists, so we aren't creating one."
	fi
fi



######### CREATE VIEW #########
if [[ $1 == 'view' ]]; then
	view=$2
	template=$3
	touch $snippets_dir/sapling/templates/$template/views/$view.html
	touch $snippets_dir/sapling/templates/$template/views/$view.css
fi
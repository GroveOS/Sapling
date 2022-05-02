 #!/bin/bash

. $(dirname "$0")/core.sh

if [[ $1 == 'template' ]]; then
	template=$2
	if [[ -f $template.php ]]; then
		if [[ -d $snippets_dir/templates/$template ]]; then
			echo "Error: Both $template.php and its associated template directory already exist. Please investigate this; we aren't proceeding."
		else
			echo "Head's up: $template.php exists, but its tempalte directory doesn't. We'll create the template directory, but be sure to link it up in the existing $template.php file; we aren't touch that."
			create_template $template
			create_template_folder $template
		fi
	fi
elif [[ $1 == 'view' ]]; then
	view=$2
	template=$3
	touch $snippets_dir/sapling/templates/$template/views/$view.html
	touch $snippets_dir/sapling/templates/$template/views/$view.css
fi
 #!/bin/bash

. sc/core.sh

if [ $1 == 'template' ]; then
	template=$2
	remove_template $template
	remove_template_folder $template
elif [ $1 == 'view' ]; then
	view=$2
	template=$3
	rm $snippets_dir/sapling/templates/$template/views/$view.html
fi
 #!/bin/bash

. $(dirname "$0")/core.sh



######### REMOVE TEMPLATE #########
if [[ $1 == 'template' ]]; then
	template=$2
	remove_template $template
	remove_template_folder $template
fi



######### REMOVE VIEW #########
if [[ $1 == 'view' ]]; then
	view=$2
	template=$3
	rm $snippets_dir/sapling/templates/$template/views/$view.html
fi
. sc/core.sh

if [[ $1 == 'template' ]]; then
	template=$2
	create_template $template
	create_template_folder $template
elif [[ $1 == 'view' ]]; then
	view=$2
	template=$3
fi
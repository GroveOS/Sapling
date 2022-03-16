. sc/core.sh

if [[ $1 == 'template' ]]; then
	template=$2
	if [[ -f $template.php ]]; then
		echo "Error: $template.php already exists. Will not continue."
	else
		create_template $template
		create_template_folder $template
	fi
elif [[ $1 == 'view' ]]; then
	view=$2
	template=$3
	touch $snippets_dir/sapling/templates/$template/views/$view.html
	touch $snippets_dir/sapling/templates/$template/views/$view.css
fi
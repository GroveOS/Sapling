# Prepare installation based on arguments
if [[ $1 == 'from-scratch' ]]; then
	git clone https://github.com/CouchCMS/CouchCMS
	mv CouchCMS/couch ./
	rm -rf CouchCMS
	couch_dir='couch'
	mkdir embed
	snippets_dir='embed'
	sapling_commands_dir='sc'
elif [[ $1 == 'alongside-couch' ]]; then
	if [[ $3 == 'default' ]]; then
		couch_dir=$2
		snippets_dir='embed'
		sapling_commands_dir='sc'
	else
		couch_dir=$2
		snippets_dir=$3
		sapling_commands_dir=$4
	fi
else
	couch_dir=$1
	snippets_dir=$2
	sapling_commands_dir=$3
fi

if [[ ! $couch_dir ]]; then
	echo "No Couch directory provided. Aborted."
	exit 0
fi

if [[ ! $snippets_dir ]]; then
	echo "No Couch snippets directory provided. Aborted."
	exit 0
fi

if [[ ! $sapling_commands_dir ]]; then
	echo "No Sapling commands directory provided. Aborted."
	exit 0
fi

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
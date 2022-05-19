 #!/bin/bash

. $(dirname "$0")/core.sh



######### UPDATE COUCH #########
if [[ $1 == 'couch' ]]; then
	git clone https://github.com/CouchCMS/CouchCMS
	cp -r CouchCMS/couch/* $couch_dir/
	rm -rf CouchCMS
fi




######### UPDATE SAPLING #########
if [[ $1 == 'sapling' ]]; then
	git clone https://github.com/GroveOS/Sapling
	cp -r Sapling/embed/sapling/* $snippets_dir/sapling/
	cp -r Sapling/sc/* $sapling_commands_dir/
	rm -rf Sapling
fi
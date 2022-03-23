 #!/bin/bash

. sc/core.sh

if [[ $1 == 'couch' ]]; then
	git clone https://github.com/CouchCMS/CouchCMS
	cp -r CouchCMS/couch/* $couch_dir/
	rm -rf CouchCMS
elif [[ $1 == 'sapling' ]]; then
	git clone https://github.com/GroveOS/Sapling
	cp -r Sapling/embed/sapling/* $snippets_dir/sapling/
	cp -r Sapling/sc/* $sapling_commands_dir/*
	rm -rf Sapling
fi

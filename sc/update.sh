. sc/core.sh

if [[ $1 == 'couch' ]]; then
	git clone https://github.com/CouchCMS/CouchCMS
	cp -r CouchCMS/couch/* $couch_dir/
	rm -rf CouchCMS
elif [[ $1 == 'sapling' ]]; then
	git clone https://github.com/GroveOS/Sapling
	cp -r Sapling/sapling/* $snippets_dir/sapling/
	rm -rf Sapling
fi
if [[ $1 == 'couch' ]]; then
	git clone https://github.com/CouchCMS/CouchCMS
	cp -r CouchCMS/couch/* couch/
	rm -rf CouchCMS
elif [[ $1 == 'sapling' ]]; then
	git clone https://github.com/GroveOS/Sapling
	Sapling/install.sh
	rm -rf Sapling
fi
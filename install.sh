cp -r Sapling/embed ./
cp -r Sapling/sc ./
for item in head header footer; do
	if [[ ! -f embed/sapling/layout/$item.html ]]; then
		mv embed/sapling/layout/$item.example.html embed/sapling/layout/$item.html
	else
		mv embed/sapling/layout/$item.example.html embed/sapling/layout/$item.updated.html
	fi
done
if [[ ! -f sc/config.sh ]]; then
	mv sc/config.example.sh sc/config.sh
else
	mv sc/config.example.sh sc/config.updated.sh
fi
rm -rf Sapling
cp -r Sapling/embed ./
cp -r Sapling/sc ./
for item in head header footer; do
	if [[ ! -f embed/sapling/layout/$item.html ]]; then
		mv embed/sapling/layout/$item.example.html embed/sapling/layout/$item.html
	fi
done
rm -rf Sapling
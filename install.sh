cp -r sapling/embed ./embed
cp -r sapling/sc ./sc
cp -r embed/* ./embed/
for item in head header footer; do
	if [[ ! -f embed/sapling/layout/$item.html ]]; then
		mv embed/sapling/layout/$item.example.html embed/sampling/layout/$item.html
	fi
done

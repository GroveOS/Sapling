# Sapling
A CouchCMS development framework for building maintainable CouchCMS websites and web apps.

## Requirements
- CouchCMS v2.3
- your Couch snippet directory must be 'embed' (at the root of your project)

## Quick start
```sh
# Create your project folder
mkdir my_project
cd my_project

# Install CouchCMS
git clone https://github.com/CouchCMS/CouchCMS
mv CouchCMS/couch ./
cp couch/config.example.php couch/config.php
rm -rf CouchCMS

# Install Sapling
git clone https://github.com/GroveOS/Sapling
rm -r Sapling/docs
rm Sapling/readme.md
mv Sapling/* ./
rm -rf Sapling
```
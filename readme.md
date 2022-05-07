# Sapling
A CouchCMS starter codebase for building maintainable CouchCMS projects.

To see it in action, visit the <a href="https://github.com/GroveOS/Sapling/tree/root/docs">Docs</a>.

## Requirements
- Familiarity with CouchCMS (couchcms.com)
- A unix like development environment that can run bash scripts:
	- Linux or Mac
	- Windows Subsystem for Linux aka WSL

## Quick install
```bash
# Create your project folder
mkdir my_project
cd my_project

# Install Sapling
git clone https://github.com/GroveOS/Sapling && Sapling/install.sh
```

## Installation -- under the hood
The Sapling/install.sh script expects the following number of arguments:
- 0 (**standard**) --> `Sapling/install.sh`
- 3 (**custom**) --> `Sapling/install.sh admin admin/snippets sap`
- 4 (**alongside**) --> `Sapling/install.sh alongside couch couch/snippets sc`

Let's discuss these three options for installation.

### 1. Standard
By providing 0 arguments, Sapling will install a standard installation with default values for the _CouchCMS directory_, _your Couch snippets directory_, and _our Sapling commands directory_.
```bash
Sapling/install.sh
```
The result is three directories with contents in our project directory like so:
- **couch/** (default CouchCMS directory)
- **embed/** (our preferred snippets directory)
- **sc/** (default Sapling commands directory)

### 2. Custom
By providing three arguments, we can specify the names of these folders to be installed, such as:
`Sapling/install.sh admin admin/snippets sap`

That would create the following structure:
- **admin/** (your preferred CouchCMS directory)
	- **admin/snippets/** (you preferred Couch snippets directory, which happens to be Couch's default)
- **sap/** (your preferred Sapling commands directory)

### 3. Alongside
By providing 4 arguments, you can install Sapling alongside an existing CouchCMS project, so long as you don't already have a directory in your snippets directory named 'sapling'. We imagine that isn't the case.

For instance, suppose you had an existing structure like so:
- **couch/**
	- **couch/snippets**
		- **couch/snippets/** my-snippets-file.html
		- **couch/snippets/** my-other-snippets-file.html
- index.php
- posts.php
- contact.php

We can install Sapling alongside this existing structure by providing 4 arguments. **Note**: your first argument of the 4 should be 'alongside'. For instance:
`Sapling/install.sh alongside couch couch/snippets sc`

That would install Sapling alongside our existing project like so:
- **couch/**
	- **couch/snippets**
		- **couch/snippets/sapling/** <-- (new)
		- **couch/snippets/** my-snippets-file.html
		- **couch/snippets/** my-other-snippets-file.html
		- sapling.html <-- (new)
- **sc/** <-- (new)
- index.php
- posts.php
- contact.php

# Sapling commands
By default, Sapling commands are located in the **sc/** directory. Sapling commands are executable, so you should be able to simply type `sc/{:command}.sh` in your terminal to execute a Sapling command.

***Important***: these commands hop around, make new files, and remove things. There's probably an elegant way of calling the script from anywhere (we're working on that), but, in the meantime, just make sure you execute the command from the root of your project directory.

Here are the available commands:
- [sc/create.sh](#sccreatesh)
- [sc/remove.sh](#scremovesh)
- [sc/update.sh](#scupdatesh)

- - -

## sc/create.sh
The create.sh command takes two arguments and can be used to create new templates or new views for templates. The first argument declares the creation type, and the second argument declares the name of your intented creation.

`bash sc/create.sh {:creation-type} {:name}`

For instance:
```bash
# Create a new template, posts.php
sc/create.sh template posts

# Create a new view, featured.html, for the newly created posts.php template
sc/create.sh view featured posts
```

Creating a new template will:
1. place an empty template file at the project's root
2. create an associated template folder in the **{:snippets}/sapling/** directory

Like so:

### posts.php
```php
<?php require_once('couch/cms.php');



<?php COUCH::invoke();?>
```

### resulting file structure
```
couch/*
docs/*
embed/
| - - - sapling/
| - - - | - - - addons/*
| - - - | - - - funcs/*
| - - - | - - - layout/*
| - - - | - - - lib/*
| - - - | - - - templates/
| - - - | - - - | - - - index/*
| - - - | - - - | - - - posts/ <------ ( *** NEW *** )
| - - - | - - - | - - - | - - - config-form.html
| - - - | - - - | - - - | - - - config-list.html
| - - - | - - - | - - - | - - - editables.html
| - - - | - - - | - - - | - - - routes.html
| - - - | - - - | - - - | - - - template.css
| - - - | - - - | - - - | - - - views/
| - - - | - - - | - - - | - - - | - - - archive.css
| - - - | - - - | - - - | - - - | - - - archive.html
| - - - | - - - | - - - | - - - | - - - folder.css
| - - - | - - - | - - - | - - - | - - - folder.html
| - - - | - - - | - - - | - - - | - - - list.css
| - - - | - - - | - - - | - - - | - - - list.html
| - - - | - - - | - - - | - - - | - - - show.css
| - - - | - - - | - - - | - - - | - - - show.html
| - - - config.json
| - - - init.html
| - - - init.php
| - - - sapling.html
index.php
install.sh
posts.php <------ ( *** NEW *** )
readme.md
sc/*
```

- - -

### sc/remove.sh

(documentation coming soon)

- - -

### sc/update.sh

(documentation coming soon)
# Sapling commands
By default, Sapling commands are located in the **sc/** directory, unless you specified a different directory name. Sapling commands are executable, so you should be able to simply type `sc/{:command}.sh` in your terminal to execute the command. **Important**: make sure your execute the command from the root of your project directory.

## sc/create.sh
We can use the create.sh command to create new templates and new views for templates. For instance:
```bash
# Create a new template, posts.php
sc/create.sh template posts
# Create a new view, featured.html for the posts.php template
sc/create.sh view featured posts
```

Creating a new template will place an empty template file at the project's root and create an associated template folder in the **{:snippets}/sapling/** directory.

### posts.php
```php
<?php require_once('couch/cms.php');
<?php COUCH::invoke();?>
```

### Project structure
- **couch/**
- **embed/**
	- **sapling/**
		- **templates/**
			- **posts/**
**sc/**

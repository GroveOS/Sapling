# Hello world web page

Let's create a simple hello world web page as our most bare-bones example of using Sapling. Nothing fancy here, but this should give you the basic tools to get started making your own Sapling project.

In this example we'll:
- install Sapling
- connect Sapling to our Couch project
- easily *sc/create.sh* a hello-world custom view (custom route)
- *<cms:extend />* some default Sapling markup
- add a bit of CSS styling for that view only

- - -

## 1. Install Sapling
Start by creating your project directory and installing Sapling within. By omitting Sapling/install.sh arguments, Sapling will assume default values which are:
1. CouchCMS dir named **/couch/**
2. Couch snippets directory at **/embed/**
3. default Sapling commands folder at **/sc/**

```bash
mkdir blog
cd blog
git clone https://github.com/GroveOS/Sapling
bash Sapling/install.sh

# This yields installs CouchCMS, Sapling files,
# and the following directory structure:
#
# blog/ <------ ( our project directory )
# - - - | couch/
# - - - | embed/
# - - - | sc/
# - - - | index.php
```

So far, all we've done is set up a blank CouchCMS project with Sapling at the ready. Next, we'll need to call upon Sapling, which we'll do in the next step.

> **Optional pro tip:** *calling upon* Sapling illustrates its "plug-in" nature. That is, you don't have to use Sapling with all your Couch templates. Rather, you call upon Sapling wherever you deem appropriate. You can even install Sapling alonside an existing CouchCMS project by passing the appropriate arguments during installation. So if you had an existing Couch project with the couch directory named 'admin', and you were using the default Couch snippets folder 'admin/snippets/', then you could install Sapling alongside without disturbing your project by navigating to the root directory of your project and:
> ```bash
> git clone https://github.com/GroveOS/Sapling
> bash Sapling/install.sh alongside admin admin/snippets sc
> ```
> That is, '**Sapling/install.sh**' '**alongside**' the Couch folder named '**admin**', with the Couch snippets folder being '**admin/snippets**', declaring '**sc**' as your Sapling commands directory.

- - -

## 2. Call upon Sapling

With our blank CouchCMS project ready, we can now call upon Sapling wherever we'd like. Let's do that in the index.php file. We'll go ahead and set up basic Couch <cms:template /> info too.

```html
<!-- Index.php -->

<?php require_once('couch/cms.php');?>

<cms:template title='Index' icon='home' order='0' />
<cms:embed 'sapling.html' />

<?php COUCH::invoke();?>
```

That's it! 🙂 We've called upon Sapling and can begin leveraging the **sc/** commands to create a hello-world view and pipe in some *hello-world-specific* CSS styling.

- - -

## 3. Two ways to say 'hello world'
We have two ways of presenting content using Sapling:
1. pre-made views
2. custom views

### Pre-made views

Pre-made views are meant to mimic the default CouchCMS views (k_is_home, k_is_page, etc) and come pre-built with each Sapling-created template in the **embed/sapling/templates/my-template/views/** directory. They use Larvel naming conventions:
- list.html (k_is_home)
- show.html (k_is_page)
- folder.html (k_is_folder)
- archive.html (k_is_archive)

### Custom views

Custom views are made by you and live alongside the pre-made views (**embed/sapling/templates/my-template/views/**). Using custom views is optional and does require a two modifications to the Couch template file you use them with -- notably a **cms:match_route** tag and a **K_IGNORE_CONTEXT** argument. We'll get to that if you're unfamiliar.

Back to our hello world example.

--

If all we wanted to say was 'hello world', we could use the pre-made **index > list.html** view, but let's use that view as a welcome page that then links to a view saying 'hello world'. This way, we can use both pre-made and custom views in this example!

First, let's set up our welcome page (index > list.html). In your text editor, navigate to **embed/sapling/templates/index/views/list.html**. It should be empty, so we should add our welcome content now.

> **Optional pro tip:** Sapling's file structure is designed specifically for use with fuzzy search. If you're using something like Sublime Text or VS Code, you can do a quick fuzzy search for `index list.html`, which should pull up the list.html file for the index.php template.

Let's write the following to that file.

```html
<!-- embed/sapling/templates/index/views/list.html -->

<html>
  <h1>Welcome to Sapling! Check out our <a href="<cms:route_link 'hello-world' />">hello-world</a> page!</h1>
</html>
```

If you're unfamiliar with the **cms:route_link** tag, that's a special tag from the [custom-routes addon](https://www.couchcms.com/docs/custom-routes/) for Couch. Sapling will use this addon to route to our hello-world custom view. Speaking of, let's create that custom view:

```bash
# make sure you call this from the root of your project
bash sc/create.sh view hello-world index
```

Think of that as saying **sc/create.sh** a **view** named **hello-world** for the **index**.php template. `bash sc/create.sh view hello-world index` It has created a hello-world.html and a hello-world.css file inside **embed/sapling/templates/index/views/**, which we'll use in a bit.

In your text editor, navigate to **embed/sapling/templates/index/views/list.html**, or fuzzy search **index list.html**. It should be blank, so let's fix that.

```html
<html>
  <p>Hello world!</p>
</html>
```

Now, hop into your browser and navigate to the home page of your project. You should see our simple "Welcome" heading we created earlier

- - -

( TO BE CONTINUED )
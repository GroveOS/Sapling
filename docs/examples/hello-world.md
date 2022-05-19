# Hello world web page

Let's create a simple hello world web page as our most bare-bones example of using Sapling. Nothing fancy here, but this should give you the basic tools to get started making your own Sapling project.

In this example we'll:
1. install Sapling
2. call upon Sapling inside a Couch template
3. *sc/create.sh* a hello-world custom view (using custom routes)
4. use *<cms:extends />* to inheret some Sapling markup
5. add a bit of template and view specific CSS

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

In your text editor, navigate to **embed/sapling/templates/index/views/hello-world.html**, or fuzzy search **index hello world**. It should be blank, so let's fix that.

```html
<html>
  <p>Hello world!</p>
</html>
```

Now, hop into your browser and navigate to the home page of your project. You should see our simple "Welcome" heading we created earlier. Now, click the hello world link, and you'll notice **it doesn't take us anywhere**! Why is this?

This is because we haven't yet defined the hello-world custom route in our index.php Couch template. To make use of Sapling's custom views, we must add a few things to the template file so Couch knows how to handle this request.

In the index.php file, we need to:
1. make the template routable (inside the template tag)
2. define a the list and hello-world route
3. add a <cms:match_route /> tag
4. pass in the K_IGNORE_CONTEXT argument to COUCH::invoke() at the bottom of the file

Once these things are taken care of, our page will behave correctly.

```html
<!-- index.php -->

<?php require_once('couch/cms.php');?>

<cms:template title='Index' icon='home' order='0' routable='1'>
  <cms:route name='list' path='' />
  <cms:route name='hello-world' path='hello-world' />
</cms:template><cms:match_route />
<cms:embed 'sapling.html' />

<?php COUCH::invoke( K_IGNORE_CONTEXT );?>
```

Now, reload the page and the link will work!

- - -

## 4. Extend some Sapling markup

Out of the box, Sapling provides a standard markup template ( **Sapling > standard.html** ) that any view can inheret via the <cms:extends /> tag. If you aren't familiar with how Couch handles template inheritance, you can [reference its documentation here](https://www.couchcms.com/forum/viewtopic.php?f=5&t=10984).

**Sapling > standard.html** is located at **embed/sapling/layout/standard.html** and includes a number of helpful blocks that can be dynamically filled or overwritten within a view that extends it.

In both our views (list.html and hello-world.html), let's apply this technique.

```html
<!-- embed/sapling/templates/index/views/list.html -->

<cms:extends 'sapling/layout/standard.html' />

<cms:block 'content'>
  <h1>Welcome to Sapling! Check out our <a href="<cms:route_link 'hello-world' />">hello-world</a> page!</h1>
</cms:block>
```

One of Sapling > standard.html's block is the 'title' block, which wraps the content of the title tag of the document. We can invoke this block and put in a quick title.

```html
<!-- embed/sapling/templates/index/views/list.html -->

<cms:extends 'sapling/layout/standard.html' />

<cms:block 'title'>Welcome to Sapling</cms:block>

<cms:block 'content'>
  <h1>Welcome to Sapling! Check out our <a href="<cms:route_link 'hello-world' />">hello-world</a> page!</h1>
</cms:block>
```

Refresh your browser, and you'll see the browser tab now reflects this.

### Embedded head, header, footer
A benefit of using the <cms:extends /> approach above is that Sapling automatically calls in blank head.html, header.html, and footer.html files that are included via <cms:embed />. These are located at **embed/sapling/layout/**. You can review the full [Sapling > standard.html](https://github.com/GroveOS/Sapling/blob/root/embed/sapling/layout/standard.html) file on your own time, but, for now, here's a concise edition to give you the gist of what it's doing: 

```html
<!-- embed/sapling/layout/standard.html -->

<html>
  <head>
    <cms:block 'head'>
      <title><cms:block 'title' /></title>
      <cms:embed 'sapling/layout/head.html />
    </cms:block>
  </head>
  <cms:block 'header'><cms:embed 'sapling/layout/head.html /></cms:block>
  <cms:block 'content' />
  <cms:block 'footer'><cms:embed 'sapling/layout/head.html /></cms:block>
</html>
```

Notice that -- like the title tag -- our **head**, **header**, and **footer** are all wrapped in their own block tags. This means that -- while we can include the same head, header, and footer among all files that extend Sapling > standard.html -- we can also choose dynamically to override or even exclude completely these sections at the view level.

For instance, in our hello-world custom view, we could extend the majority of the markup provided but choose to put in our own custom head tag content if we really want:

```html
<!-- embed/sapling/templates/index/views/hello-world.html -->

<cms:extends 'sapling/layout/standard.html' />

<cms:block 'head'>
  <title>Here's our own title</title>
</cms:block>

<cms:block 'content'>
  <p>Hello world!</p>
</cms:block>
```

You can add whatever you like to your head, header, and footer files, including links to CSS and JS files, external sources, style tags, etc.

## 5. CSS styling for the template and the view

While we can embed a global CSS file in the head file, CSS styling can also be applied on the template and view level. This can be handy when you want to apply styling to only a particular area of the project. Each template's **template.css** file is located at the root of the template directory, and each view's **{:view}.css** file is located alongside that particular view (in the views directory).

Let's use our index template's **template.css** file to inject some template-scoped CSS styling. That file is located at **embed/sapling/templates/index/template.css** (or fuzzy search something like "index template css").

```css
/* embed/sapling/templates/index/template.css */
body {
  text-align: center;
}
```

Refresh the browser page, and you'll see we have centered text. And note, that template-scoped CSS won't affect other templates you happen to create. So, for instance, posts.php view files will still have regularly-aligned text.

Let's use our hello-world file's **hello-world.css** file to inject some view-scoped CSS styling. That file is located at **embed/sapling/templates/index/views/hello-world.css** (or fuzzy search something like "hello world css").

```css
/* embed/sapling/templates/index/views/hello-world.css */
body {
  text-align: right;
  font-size: 48pt;
}
```

Now, text is aligned in the center for all index views *except the hello-world custom view*, which is overriding the template.css ([embrace the cascade](https://css-tricks.com/dont-fight-the-cascade-control-it/)) and is applying its own styling.


- - -

## Conclusion

Okay, I'll admit, that was a little long for a hello world example, but the techniques discussed above constitue about 80% of what Sapling is used for typically, so that should get the ball rolling toward making your own fully functional Sapling site!

More examples to come. You're welcome to write any comments or questions to [sapling@groveos.com](mailto:sapling@groveos.com).
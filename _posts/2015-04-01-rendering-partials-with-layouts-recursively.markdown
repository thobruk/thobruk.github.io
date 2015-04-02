---
layout:     post
title:      Rendering Partials With Layouts Recursively in Rails 4
date:       2015-04-01 18:37:00
summary:    Rendering a partial with a layout recursively quite often ends in tears.
categories: rails
---
So, gather around and I will tell you a story of a terrible curse. Or not:

###TL;DR

Add this to your application helper and use it instead of `render` whenever there's a chance you will call a partial and try to wrap it in a layout.

{%highlight ruby%}
  def inside(options)
    parent_view_flow = view_flow
    self.view_flow = ActionView::OutputFlow.new
    yield
    output = render options
    self.view_flow = parent_view_flow
    output
  end
{%endhighlight%}

*Takes the current view_flow, stores it, starts a new one, renders your partial, then puts everything back the way it was*

![We have to go deeper](http://s2.quickmeme.com/img/e7/e7633bedf897bb24ce668ac9c5df6bf88a58ff7e114d27606a756f4c4888a3f1.jpg) 

###Layouts and Partials

Normally, it's not so hard to apply a layout to a partial. Why would I apply a layout to a partial ? Am I nuts ? Possibly. Let me explain. Let's say I have a bunch of things I would like to have similarly formatted. Imagine (if you will) a scenario in which I need a number of things formatted in the same way. Say, you were trying to display something that looked like a file heirarchy. You might have a Folder object that contained File objects. Yes, that sounds reasonable. The folder object has a 'name' property, just like the File object does. So, potentially one could use a common template for Files and Folders. Now, I warn you, I like haml with a bit o Bootstrap. So that's how this is going to go down. Let's say I have a template thus, for rendering a filesytem thingy and it's contents.

`shared/_filesystem_object.html.haml`
{%highlight haml%}
.row
  .col-xs-3
    = content_for :type
  .col-xs-9
    = content_for :name
.row
  .col-xs-2
  .col-xs-10
    = content_for :contents
{%endhighlight%}

And a partial for rendering a folder
`folder/_folder.html.haml`
{%highlight haml%}
-content_for: type do
  = folder.type
-content_for :name do
  = folder.name
-content_for :contents do
  = render folder.contents # this is where it's all going to go horribly wrong
=render partial: 'shared/filesystem_object'
{%endhighlight%}

Rails is smart enough to work out what to do at `render folder.contents`. It's going to render a collection, calling the `folders/_folder` partial for each Folder in contents. There is another way to do this. I could call `render partial: 'folder/_folder', layout: 'shared/filesystem_object'` instead. The results would be the same - bitterly disappointing.

Let's take a look at some screenshots.

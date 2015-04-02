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

Normally, it's not so hard to apply a layout to a partial. Why would I apply a layout to a partial ? Am I nuts ? Possibly. Let me explain. Let's say I have a bunch of things I would like to have similarly formatted = Use case for a layout. Say, you were trying to display something that was self-referencing. Like an Item that has_many items. Yes, that sounds reasonable. The Item object has a 'name' property. 

```
rails g scaffold Item name item:references
```

Now, I warn you, I like haml with a bit o Bootstrap. So that's how this is going to go down. Let's say I have a template thus, for rendering a filesytem thingy and it's contents.

`shared/_data_card.html.haml`
{%highlight haml%}
.well
  .h1
    = content_for :name
  .h2
    Items:
    = content_for :contents
{%endhighlight%}

And a partial for rendering an Item

`item/_item.html.haml`
{%highlight haml%}
-content_for :name do
  = item.name
-content_for :contents do
  = render item.items # this is where it's all going to go horribly wrong
=render partial: 'shared/data_card'
{%endhighlight%}

A model.

`models/item.rb`
{%highlight ruby%}
class Item < ActiveRecord::Base
  belongs_to :item
  has_many :items
end
{%endhighlight%}

A bit of a tweak to 

`views/items/show.rb`
{%highlight haml%}
=render @item
{%endhighlight%}

A little prep in rails console or migrations, whatevs.
{%highlight ruby%}
Item.create(name: 'bottom_item', item: Item.create(name: 'middle_item', item: Item.create(name: 'top_item')))
{%endhighlight%}
*Probably need to work on making these names a bit less 'itemy'*

Rails is smart enough to work out what to do at `render folder.contents`. It's going to render a collection, calling the `folders/_folder` partial for each Folder in contents. There is another way to do this. I could call 

```
render partial: 'folder/_folder', layout: 'shared/filesystem_object'
``` 

instead. The results would be the same - bitterly disappointing.

Let's take a look at some screenshots.

![My helpful screenshot]({{ site.url }}/images/04022015shot1.png)

Well, that's what we were HOPING for. But actually this is what you end up with:

![My helpful screenshot]({{ site.url }}/images/04022015shot2.png)

### WTF ?

Let me tell you WTF. It's pretty simple. Rails has a thing called `view_flow` which is an instance of `ActionView::OutputFlow`. When you do `content_for` it puts the result of that block into the `view_flow.content hash`. If `content_for` gets called multiple times it concatenates the results. Are you feeling me ? So what happens in our example. Well, the following happens:

* `content_for` first item's :name is called.
* `content_for` first item's content is called BUT it has to do this first:
  * `content_for` second item's :name is called (uh oh. what's in the `view_flow` **now** ?)
  * `content_for` second_item's :content is called BUT it has to do this first: (oh dear lord)
  * etc...
  * render all the crap above that's been mashed together 
* render all the crap AGAIN

You get the picture...

### So get on with it already.

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

This little fella takes a copy of the `view_flow` and makes a fresh one for your recursive call, and puts it back once the result is computed and output. That stops stuff falling all over itself. How do we use it ? Easy. Let's refer back to this partial:

`item/_item.html.haml`
{%highlight haml%}
= inside(partial: 'layouts/data_card') do
  -content_for :name do
    = item.name
  -content_for :contents do
    = render item.items # this is where it's all going to go swimmingly.
{%endhighlight%}

`view_flow` feels horribly like a global variable, doesn't it ? Perhaps there's a way to make it local-er.

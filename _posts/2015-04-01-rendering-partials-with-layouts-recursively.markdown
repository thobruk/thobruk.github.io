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

A model
`models/item.rb'
{%highlight ruby%}
class Item < ActiveRecord::Base
  belongs_to :item
  has_many :items
end
{%endhighlight%}

A bit of a tweak to `views/items/show.rb`
{%highlight haml%}
=render @item
{%endhighlight%}

A little prep in rails console or migrations, whatevs.
{%highlight ruby%}
Item.create(name: 'bottom_item', item: Item.create(name: 'middle_item', item: Item.create(name: 'top_item')))
{%endhighlight%}
* Probably need to work on making these names a bit less 'itemy' *

Rails is smart enough to work out what to do at `render folder.contents`. It's going to render a collection, calling the `folders/_folder` partial for each Folder in contents. There is another way to do this. I could call `render partial: 'folder/_folder', layout: 'shared/filesystem_object'` instead. The results would be the same - bitterly disappointing.

Let's take a look at some screenshots.

---
layout:     post
title:      Rendering Partials With Layouts Recursively in Rails 4
date:       2015-04-01 18:37:00
summary:    Rendering a partial with a layout recursively quite often ends in tears.
categories: rails
---
So, gather around and I will tell you a story of a terrible curse. Or not:

###TL;DR

Add this to your application helper and use it instead of render whenever there's a chance you will call a partial and try to wrap it in a layout.

{% highlight ruby %}
module ApplicationHelper
  def inside(*params)
    # to be continued...
  end
end
{% endhighlight %}

###Layouts and Partials

Normally, it's not so hard to apply a layout to a partial. Why would I apply a layout to a partial ? Am I nuts ? Possibly. Let me explain. Let's say I have a bunch of things I would like to have similarly formatted. Imagine (if you will) a scenario in which I need a number of things formatted in the same way. Say, you were trying to display something that looked like a file heirarchy. You might have a Folder object that contained File objects. Yes, that sounds reasonable. The folder object has a 'name' property, just like the File object does. So, potentially one could use a common template for Files and Folders. Now, I warn you, I like haml. So that's how this is going to go down. Let's say I have a template thus:

{% highlight ruby %}
# coming soon !
{% endhighlight %}



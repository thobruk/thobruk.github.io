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

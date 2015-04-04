---
layout:     post
title:      "RecordNotUnique - Expect the unexpected."
date:       2015-04-03 21:19:00
summary:    Exceptions should not be expected. Except when they are.
categories: rails
---
I have heard and even said to other developers that 'exceptions should not be expected'. Some languages rely on exceptions
for message passing. I can just see the little buggers bubbling up like the bubbles in a pint of Guinness. Mmmm...

Where were we ? Right. Exceptions (in Ruby) should not be expected. What does that mean exactly ? Let me give you an example.
I have been trying to DRY up my basic controller world. I keep using the usual new, create, edit etc. actions and I wanted to make those as tight
and readable as possible. And then there was this one:

{%highlight ruby%}
class Item < ActiveRecord::Base
    validates_uniqueness_of :name
end
{%endhighlight%}


{%highlight ruby%}
def create
    @item = Item.new(item_params)

    if @item.save
        redirect_to @item, notice: 'Item was successfully created/updated.'
    else
        render :new
    end
end
{%endhighlight%}

This looks all fine and dandy on the face of it. Look! I even put a uniqueness validation on `Item.name`. What could possibly go wrong ? This is *textbook*. I
bet even the scaffold generator would make something that looked a lot like that (it does). Well, there's a reasonable
chance that mysql will throw ActiveRecord::RecordNotUnique in your face. The problem arises because *time elapses* between the validation and the save.
Someone could squeak a record in there that has the same 'name' as your record and then you try to save and **BOOM**. Now, old-school developers like me
hate the idea of CHECKING and then SAVING. We just used to setup the database with constraints to handle these sort of validations and wait for it to throw an
exception. It was atomic and efficient. But I guess that just ain't cool anymore.

So, there's a non-zero chance of your exception getting thrown and the user is then plunged in to an error page. No nice form with error messages on the input
fields so you can work out what happened. Well, Mr Smarty Pants, where did your zen-like lack of exception handling get you ?? Let's tweak up our action a bit:

{%highlight ruby%}
def create
    @item = Item.new(item_params)

    if @item.save
        redirect_to @item, notice: 'Item was successfully created/updated.'
    else
        render :new
    end
rescue ActiveRecord::RecordNotUnique
    flash[:notice] = 'Unable to create Item'
    render :new
end
{%endhighlight%}

Well, this is a bit better. At least they get a flash message, but it's not neatly sitting on the form field. I suppose I could hack around with the object's error
collection and try to stick something in the right spot. I want the best user experience possible.
I could even parse the error message and try to work out which field was affected by the error. That would
be a pain in the arse, because all database servers will throw a different message. Not very portable.
Either way this definitely violates 'exceptions should not be expected'.

### Transactions, dude.

Let's try whacking a transaction on this sucker. Then the whole darned database will be halted/locked - whatever -  while you CHECK and SAVE.
That's fine I suppose. Feels like overkill to me. It has it's own set of problems, but from a logic standpoint it's pretty good. I don't need
to catch the exception anymore because it can't happen.

{%highlight ruby%}
def create
    @item = Item.new(item_params)

    Item.transaction do
        if @item.save
            redirect_to @item, notice: 'Item was successfully created/updated.'
        else
            render :new
        end
    end
end
{%endhighlight%}

Wait - what? The [ActiveRecord](http://api.rubyonrails.org/classes/ActiveRecord/Validations/ClassMethods.html#method-i-validates_uniqueness_of)
docs point out that even this might not work depending on your transaction settings. I don't know about you, but I'm thoroughly depressed. I mean this
is core to the job of Rails and yet there's no a super-slick solution. There must be a lot of OCD developers out there with an uneasy feeling
as they go home at night.

### Setting Expectations

So who said "exceptions should not be expected" ? Who is responsible for this heart-rending rubric ?
[Great Answer Here](http://programmers.stackexchange.com/a/184714) from [Mike Partridge](http://programmers.stackexchange.com/users/34183/mike-partridge)

<blockquote>
  <p>
    We believe that exceptions should rarely be used as part of a program's normal flow; exceptions should be reserved for unexpected events. Assume that an uncaught exception will terminate your program and ask yourself, "Will this code still run if I remove all the exception handlers?" If the answer is "no," then maybe exceptions are being used in nonexceptional circumstances.
  </p>
  <footer><cite title="The Pragmatic Programmer">The Pragmatic Programmer</cite></footer>
</blockquote>

And here comes the science...

<blockquote>
  <p>
    [A]n exception represents an immediate, nonlocal transfer of control - it's a kind of cascading goto. Programs that use exceptions as part of their normal processing suffer from all the readability and maintainability problems of classic spaghetti code. These programs break encapsulation: routines and their callers are more tightly coupled via exception handling.
  </p>
  <footer><cite title="The Pragmatic Programmer">The Pragmatic Programmer</cite></footer>
</blockquote>

*Sigh.* How am I going to get rid of this dirty feeling? I have been scrubbing for hours BUT IT WON'T COME OFF ! I think the answer must come from within.

### Slow, slow, quick, quick, slow

[Exceptions are slow, too.](http://simonecarletti.com/blog/2010/01/how-slow-are-ruby-exceptions/)
Although this isn't really a problem in our case. We are doing something at human-scale speeds. This exception-raising probably won't ever become
a serious performance issue in our example. When it takes our user whole seconds to complete their form, who is going to miss a few microseconds ?

### No hope of rescue

There's always `rescue_from`, I suppose. But that seems even worse. Flow would jump out of your function and off into some global error handler. I
mean, seriously ? Are you nuts ?

{%highlight ruby%}
class ApplicationController < ActionController::Base
  rescue_from ActiveRecord::RecordNotUnique, with: :spaghetti_code

{%endhighlight%}

### If at first you don't succeed...

Some have suggested using 'retry' to solve the issue. The validation will pass the first time, raise the error, retry, then fail the second time resulting in
the requisite error messages that are useful to the user. That's 2n SQL queries, on the rare occasion that someone beats us to it and n queries (where
n depends on the number of validations you do against the database) normally but it does result in a much improved user experience. And that's a trade-off I can live with. I don't mind doing more work if it benefits the user.

{%highlight ruby%}
def create
    retries ||= 2
    @item = Item.new(item_params)

    if @item.save
        redirect_to @item, notice: 'Item was successfully created/updated.'
    else
        render :new
    end
rescue ActiveRecord::RecordNotUnique
    retry unless (retries-=1).zero?
end
{%endhighlight%}

Be careful ! Make sure you have unique constraint defined ! At least it will only try once and then stop. In the interest of DRYness we should probably
take the re-try wrapper and make it a helper or something. That `create` action is looking pretty skinny.

{%highlight ruby%}
def retry_once_on(exception)
    retries ||= 2
    yield
rescue exception
    retry unless (retries-=1).zero?
end

def create
    retry_once_on(ActiveRecord::RecordNotUnique) do
        @item = Item.new(item_params)

        if @item.save
            redirect_to @item, notice: 'Item was successfully created/updated.'
        else
            render :new
        end
    end
rescue

end
{%endhighlight%}


### Further reading

[https://robots.thoughtbot.com/the-perils-of-uniqueness-validations](https://robots.thoughtbot.com/the-perils-of-uniqueness-validations)
[https://robots.thoughtbot.com/save-bang-your-head-active-record-will-drive-you-mad](https://robots.thoughtbot.com/save-bang-your-head-active-record-will-drive-you-mad)
[http://blog.mirthlab.com/2012/05/25/cleanly-retrying-blocks-of-code-after-an-exception-in-ruby/](http://blog.mirthlab.com/2012/05/25/cleanly-retrying-blocks-of-code-after-an-exception-in-ruby/)

---
layout:     post
title:      "RecordNotUnique - Expect the unexpected."
date:       2015-04-03 21:19:00
summary:    Exceptions should not be expected. Except when they are.
categories: rails activerecord
---

### TL;DR

Not all validations are created equal. Some hit the database and others don't. You need to know which is which. Add this to the idea that "exceptions should not be expected" and you are talking about some serious cognitive dissonance. Consider using a distributed [Mutex managed by your database server](https://github.com/mceachen/with_advisory_lock) to really do a good job. Otherwise a simple `retry` will suffice.
*UPDATE* Due to rails weirdness, retry can actually cause an infinite loop scenario. Be careful.

### Beer

I have heard and even said to other developers that 'exceptions should not be expected'. Some languages rely on exceptions for message passing. I can just see the little buggers bubbling up like the bubbles in a pint of Guinness. Mmmm...

Where were we ? Right. Exceptions (in Ruby) should not be expected. What does that mean exactly ? Let me give you an example. I have been trying to DRY up my basic controller world. I keep using the usual new, create, edit etc. actions and I wanted to make those as tight and readable as possible. And then there was this one:

{%highlight ruby%} 

class Item < ActiveRecord::Base 
    validates_uniqueness_of :name 
end 

{%endhighlight%}

{%highlight ruby%} 

def create @item = Item.new(item_params)

    if @item.save
        redirect_to @item, notice: 'Item was successfully created/updated.'
    else
        render :new
    end

end 

{%endhighlight%}

This looks all fine and dandy on the face of it. Look! I even put a uniqueness validation on `Item.name`. What could possibly go wrong ? This is *textbook*. I bet even the scaffold generator would make something that looked a lot like that (it does). Well, there's a reasonable chance that mysql will throw ActiveRecord::RecordNotUnique (or many others - how woudja like an InvalidForeignKey ?) in your face. The problem arises because *time elapses* between the validation and the save. Someone could squeak a record in there that has the same 'name' as your record and then you try to save and **BOOM**. Now, old-school developers like me hate the idea of CHECKING and then SAVING. We just used to setup the database with constraints to handle these sort of validations and wait for it to throw an exception. It was atomic and efficient. But I guess that just ain't cool anymore.

So, there's a non-zero chance of your exception getting thrown and the user is then plunged in to an error page. No nice form with error messages on the input fields so you can work out what happened. Well, Mr Smarty Pants, where did your zen-like lack of exception handling get you ?? Let's tweak up our action a bit:

{%highlight ruby%} 

def create @item = Item.new(item_params)

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

Well, this is a bit better. At least they get a flash message, but it's not neatly sitting on the form field. I suppose I could hack around with the object's error collection and try to stick something in the right spot. I want the best user experience possible. I could even parse the error message and try to work out which field was affected by the error. That would be a pain in the arse, because all database servers will throw a different message. Not very portable. Either way this definitely violates 'exceptions should not be expected'.

### Transactions, dude.

Let's try whacking a transaction on this sucker. Then the whole darned table/database will be locked while you CHECK and SAVE. Feels like it might work. I don't need to catch the exception anymore because it can't happen...right?

{%highlight ruby%} 

def create @item = Item.new(item_params)

    Item.transaction do
        if @item.save
            redirect_to @item, notice: 'Item was successfully created/updated.'
        else
            render :new
        end
    end

end 
{%endhighlight%}

Wait - what? The [ActiveRecord](http://api.rubyonrails.org/classes/ActiveRecord/Validations/ClassMethods.html#method-i-validates_uniqueness_of) docs point out that this won't work. A transaction is not your tool here. Since the validation doesn't fail in any sense at a database level, the transaction proceeds unimpeded. You can also try `with_lock` but that won't cut it either as it only seems to lock the local copy of the model.

### Setting Expectations

So who said "exceptions should not be expected" ? Who is responsible for this heart-rending rubric ? [Great Answer Here](http://programmers.stackexchange.com/a/184714) from [Mike Partridge](http://programmers.stackexchange.com/users/34183/mike-partridge)

> We believe that exceptions should rarely be used as part of a program's normal flow; exceptions should be reserved for unexpected events. Assume that an uncaught exception will terminate your program and ask yourself, "Will this code still run if I remove all the exception handlers?" If the answer is "no," then maybe exceptions are being used in nonexceptional circumstances.
>
> <footer><cite title="The Pragmatic Programmer">The Pragmatic Programmer</cite></footer>

And here comes the science...

> [A]n exception represents an immediate, nonlocal transfer of control - it's a kind of cascading goto. Programs that use exceptions as part of their normal processing suffer from all the readability and maintainability problems of classic spaghetti code. These programs break encapsulation: routines and their callers are more tightly coupled via exception handling.
>
> <footer><cite title="The Pragmatic Programmer">The Pragmatic Programmer</cite></footer>

*Sigh.* How am I going to get rid of this dirty feeling? I have been scrubbing for hours BUT IT WON'T COME OFF ! I think the answer must come from within.

### Slow, slow, quick, quick, slow

[Exceptions are slow, too.](http://simonecarletti.com/blog/2010/01/how-slow-are-ruby-exceptions/) Although this isn't really a problem in our case. We are doing something at human-scale speeds. This exception-raising probably won't ever become a serious performance issue in our example. When it takes our user whole seconds to complete their form, who is going to miss a few microseconds ?

### No hope of rescue

There's always `rescue_from`, I suppose. But that seems even worse. Flow would jump out of your function and off into some global error handler. I mean, seriously ? Are you nuts ? Besides this smacks of defeatism. "This won't happen very often. The customer's convenience is less important than mine."

{%highlight ruby%} 

class ApplicationController < ActionController::Base 

    rescue_from ActiveRecord::RecordNotUnique, with: :spaghetti_code

{%endhighlight%}

### If at first you don't succeed...

Some have suggested using 'retry' to solve the issue. The validation will pass the first time, raise the error, retry, then fail the second time resulting in the requisite error messages that are useful to the user. That's 2n SQL queries, on the rare occasion that someone beats us to it and n queries (where n depends on the number of validations you do against the database) normally but it does result in a much improved user experience. And that's a trade-off I can live with. I don't mind doing more work if it benefits the user.

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
    raise 
end 

{%endhighlight%}

Be careful ! Make sure you have unique constraint defined ! At least it will only try once and then stop if it isn't. In the interest of DRYness we should probably take the re-try wrapper and make it a helper or something. That `create` action is looking pretty skinny. And `retry_once_on` can be used in all my other `create` and `update` actions.

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

    end
end 
{%endhighlight%}

### Insane in the membrane

I'm still not loving this. Another developer might well look at this and wonder why the hell I would bother retrying a failed `save`. The code doesn't change anything, it just retries.

> The definition of insanity is doing the same thing over and over and expecting different results
>
> <footer><cite title="No Einstein">Not Albert Einstein</cite></footer>

In fact, when you think about it, Einstein is the [least likely person](http://www.salon.com/2013/08/06/the_definition_of_insanity_is_the_most_overused_cliche_of_all_time/) to say that given his preoccupation with space and time. Let me change that quote:

> The definition of polling is doing the same thing over and over and expecting different results
>
> <footer><cite title="Rube Boy">Rube Boy</cite></footer>

The old saw discounts the possibility of external change. Not all change comes from within. I guess my coworker might **think** I was insane because the change that might happen is not apparent. The `retry` code is not *as* self-documenting as the simple `rescue` version.

### Mutual Exclusion

What's really needed here is a Mutex or Mutual Exclusion. This can be achieved in a couple of ways. There's a neat gem called [`with advisory lock`](https://github.com/mceachen/with_advisory_lock) that lets you create a named lock either in mySQL or Postgres (and even sqlite with the aid of file-based locks). And I'm sure a solution for MongoDB is an option. Alternatively you can manage Mutex using a table that records who has a lock on what as suggested [here](http://makandracards.com/makandra/1026-simple-database-mutex-mysql-lock). There's no point using a Ruby Mutex because there may be multiple servers running your code and Ruby Mutexes aren't distributed.

{%highlight ruby%}

def create @item = Item.new(item_params)

    Item.with_advisory_lock(:creating) do
        if @item.save
            redirect_to @item, notice: 'Item was successfully created/updated.'
        else
            render :new
        end
    end

    rescue

end 
{%endhighlight%}

So, in the end, our `retry` solution is simple and effective and maybe uses more SQL queries than you might want. On the other hand `with_advisory_lock` would do it with slightly less SQL queries, more expressive syntax and might even satisfy the Exception-phobic. It's surprising what a can of worms database-querying validations makes and I'm even more surprised that we are all casually ignoring this issue. Dealing with this problem should be a core feature of Rails. The advisory lock solution should probably wrap all ActiveRecord transactions that perform datebase validations before a database operation.

**Footnote:** A further wrinkle occurs when the validation passes and the insert fails permanently. Rails' validates_uniqueness_of constraint uses BINARY comparison which is different to the comparison mode the index uses. Sometimes (especially with trailing spaces) the BINARY comparison passes and the insert fails. Retry. Same thing happens = infinite loop. Why is nothing ever simple ? I had some ideas about this in another post.

### Further reading

[https://github.com/rails/rails/commit/392eeecc11a291e406db927a18b75f41b2658253](https://github.com/rails/rails/commit/392eeecc11a291e406db927a18b75f41b2658253)

[https://robots.thoughtbot.com/the-perils-of-uniqueness-validations](https://robots.thoughtbot.com/the-perils-of-uniqueness-validations)

[https://robots.thoughtbot.com/save-bang-your-head-active-record-will-drive-you-mad](https://robots.thoughtbot.com/save-bang-your-head-active-record-will-drive-you-mad)

[http://blog.mirthlab.com/2012/05/25/cleanly-retrying-blocks-of-code-after-an-exception-in-ruby/](http://blog.mirthlab.com/2012/05/25/cleanly-retrying-blocks-of-code-after-an-exception-in-ruby/)
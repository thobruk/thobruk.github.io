---
layout:     post
title:      "Getting the current_user or current_admin_user into your production logs."
date:       2016-02-26 08:01
summary:    With a little jiggling you can see who is logged-in in your logfile.
categories: rails logger
---

### Conspicuously Absent

I was trying to debug a problem that was impacting a specific user this morning and realized there was no good way to tell from the production logs which user had
issued any particular request. I'm using ActiveAdmin for this project, which is using Devise for authentication, which uses Warden. Warden's features don't appear to
be available at 'log-time'. But, with a bit of digging in the cookie jar we can pull the current admin_user.id out without too much hassle. Some posited solutions were a little
bit over-designed for my liking. I hate running around in half a dozen files to find things, so I feel like this is the easiest to understand solution. Stick this in `config/initializers/logging.rb`

{%highlight ruby%}
Rails.configuration.log_tags = [
    lambda { |req|
      session_key = Rails.application.config.session_options[:key]
      session_data = req.cookie_jar.encrypted[session_key]
      warden_data = (session_data["warden.user.admin_user.key"] || [[]])
      admin_user = warden_data[0][0]
      "u: #{admin_user || 0}"
    }
]
{%endhighlight%}

#### Thanks to...

http://stackoverflow.com/questions/10811393/how-to-log-user-name-in-rails
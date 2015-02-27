# Wordpress Posts to JSON

This plugin really simply goes through all the posts grabs the data from the database and dumps it into a file in a json format. If you want to customise it you'll need to do it through the actual file.

### How it works

The plugin uses wordpress actions. One on init to check if the file called posts.json actually exists. If it does then it will simply generate the json when a post is publish, updated or deleted.

### Why?

I made this because I was tired of how bad the wordpres search is. In my fustration I thought it'd be cool to make a static file of all the posts and store what data I needed in a json data format. From here I use ajax to request it, Hand it over to Lunr.js a really cool javascript full text document searcher.

Search is now quicker, doesn't have client requests. The only problem is that it hangs the front-end when the document has over 400 posts in it. But if you do plan to take this approach then use a web worker to offload some of the work in javascript.

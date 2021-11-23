# Photography Blog #

Welcome to my photography blog!

This project is for my client-server class and is designed to run on an Apache server with a PostgreSQL database.

<br>
<br>

## API ##
- - -

All of the API endpoints are located in `/blog/api/`

<br>

### Methods for user ###
Endpoint `/blog/api/user/`

`Request type`
```
behavior    |   params    |    response
```
`GET`
```
Gets the name of the user currently logged in    |    none    | username OR null
```
`POST`
```
Login Attempt   |    username, password    |    true if login success, false otherwise
```
`PUT`
```
Register New User    |    username, password    |    true if register success, false if username already exists
```
`DELETE`
```
Log out    |    none    |    nothing
```

- - -

### Methods for posts ###
Endpoint `/blog/api/post/`

<br>

`Request type`
```
behavior    |   params    |    response
```
`GET`
```
Gets all blog posts    |    none    | JSON encoded array of all blog posts
```
```
Get all posts by user    |    username    | JSON encoded array of posts by user OR Invalid username if user doesn't exist
```
`POST`
```
New Post   |    post_title, extra    |    Success message OR error_text: "error_message" if missing params or not logged in
```
`PUT`
```
Edit Post    |    post_title, post_text, post_id    |   Success message OR error_text: "error_message" if missing params or not logged in as the original poster
```
`DELETE`
```
Delete Post    |    id    |     Success message OR error_text: "error_message" if missing params or not logged in as the original poster
```

#### <b> Note about param `extra`: </b> <br> ####
`extra` should be a JSON object containing the following params:
- `title`: Title of the blog post as a string
- `image`: a base64 encoded string of any image type file
- `image type`: the file extension of the image including the period. ex: `.jpg`

I did not implement server-side file type validation for the `image` field because it can be hard to verify the contents of a base64 encoded string, so be gentle with what you upload. <br> *Also make sure to strip any leading metadata from your base64 encoded string before uploading it or you will get a broken image.*

<br>

#### <b> Design note about `editing posts` </b> ####
The purpose of this blog is to share your photography with others. With that in mind, I decided that if someone wanted to change the image they had uploaded originally to their post that it would make more sense to simply make a new post with the new image and delete the old one if they wanted to. For that reason only the `post title` and the `post body` are editable, the `image` stays the same.


- - -

### Methods for Comments ###
Endpoint `/blog/api/comment/`

<br>

`Request type`
```
behavior    |   params    |    response
```
`GET`
```
Get text of individual comment    |    comment_id    | comment text as plaintext OR error_text if missing param or invalid id
```
```
Get all comments on a specific post    |    post_id    | JSON encoded array of comments OR error_text if missing param or invalid id
```
`POST`
```
New Comment   |    post_id, comment_text    |    Success message OR nothing if missing param or not logged in
```
`PUT`
```
Edit Comment    |    id, comment_text    |   Success message OR nothing if missing param or not logged in as original commenter
```
`DELETE`
```
Delete Comment    |    id    |     Success message OR nothing if missing param or not logged in as original commenter
```
<br>

#### <b> Design note about comment's `post_id` </b> ####
The given SQL database schema did not have any column to associate a comment with a post, even though you comment on a post.  But the schema did include a `comment_date` column which I don't use in my front-end. So I made a couple functions in my `lib.php` library encode a `post_id` as a `date` and decode a `date` to a `post_id`. I realize this isn't a great solution, and more of a "hack" but the goal was to get it to work without having to edit the datebase schema, which it does.

<br><br>

## UI/UX Design Notes ##

* A floating `new post` button will show up on the home screen in the bottom left corner if you are logged in.
* Floating `edit` and `delete` buttons will show up in the bottom left corner of the page when viewing a post that you created.
* In-line `edit` and `delete` buttons will show up for any comments you have made when viewing the blog post while logged in.
* You can search any user profile for all their posts without logging in, but trying to click on the `profile` link in the nav-bar without being logged in will ask you to login to access your profile.
* All of these buttons are conditionally servered in PHP based on the account you are logged in with, that way someone can't simply use the browser dev tools to "unhide" the buttons.
* I built the front-end in Bootstrap Studio, so if you notice odd styling or linking conventions in the HTML that is why.
  * I also used a couple community made components, for the `gradient navbar` and the `featured posts slider`.

<br><br>

## Project File Structure ##

* All the pages and library files are located in `/blog/`
* User uploaded images are stored in `/blog/images/` and named by their associated `post_id`. Image file paths are stored in the DB
* The API endpoints are located in `/blog/api/`
* All front-end assets are located in `/blog/assets/`

<br><br>

## Other Design Notes ##
* I've done a decent amount of development using vanilla JS, but very little in PHP before. For this reason I decided to use minimal amounts of JS and as much PHP as I could in order to get more experience with it.
* I moved all of the actual functionality of the API into functions located in `/blog/api_lib.php`. That way I could call those same functions to render all the content server side and avoid having to call it and render it using Javascript on the client side. I was going to use `php_curl` to do this, but I decided that it was probably more elegant to use functions rather than call my own API internally and have to deal with parsing all the results.
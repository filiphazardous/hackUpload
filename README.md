hack_upload
===========

What is this?
A Drupal 8 module for simple file upload. 

Why would I want it?
It is currently useful for combining Drupal 8 with REST based web apps. 
However, if Drupal 8 does get support for a more REST-like way to upload images
(or other files), this module will be deprecated by that.

And how does it work?
The module does not change the db schema in any way. What it does, is setting 
up a form for uploading a single file (jpg, gif or png - but that's easy to
change). An unauthenticated user can get access to the form, but in the form
logic, we abort unless the user has provided credentials via native login or at
the very least HTTP Basic Auth (which is one of many hacks in the module).

The file is saved as a temporary file, but in a public path. (Don't know if
this was necessary.) The return value is a JSON object with the uuid and uri of
the file. These can be used when POSTing och PATCHing a node via Drupal 8's 
REST interface. When this is done, the file is going to gain permanent status.

What's left?
Setting up cron to clean out temporary files older than X minutes.
Setting up a config for the path in which to save the files.
Setting up a config for the file formats to accept.
- or -
Just fix the REST based file upload already... :-)

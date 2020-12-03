# contact-form

This application is displaying a contact form with basic HTML validation. 
A Javascript validation was meant to be added also.

When the form is submitted by the user a server validation will check if all required data
is entered and the fields are in the correct format. If validation fails, the user is
redirected ti the form with the submitted data filled in and error messages highlighting the missing/invalid fields.

Then is checked if the user already exists in the "users" table by email. The email address is unique. If the user
already exists, the data is updated (name, phone, etc), otherwise a new record is created.

After the user is saved/updated, an email is sent with the message. The admin email parameters are set
in the "conf/config.php".

Database design is in "database" folder. It contains a "users" table which holds the user information.
The "email" column is unique, so cannot be twice in the table and also speeds up email lookup. 
There is also a double index on the newsletter and email columns to speed up select queries when sending newsletters. 
(for example trying to select all users who checked in for newsletters)

A second table can be created for newsletters storing the user ID, newsletter content (or ID) and date/time of
the newsletter.

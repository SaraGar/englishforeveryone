# englishforeveryone
First project using Symfony 4

To use the project, you need to create a database and specify the user, password and database name in .dev file, line 27.
After that, execute php bin/console doctrine:schema:update --force to create the tables.
Populate the tables with the initial set on data that you can find inside the file dataSeeding.sql, on the project root.

Note that this is a work in progress, and also my first project using Symfony 4. Any advice would be greatly appreciated!

TODO list:
    • Remove CDNs and split js and css into separate files.
    • Add mail sending
    • Cron to reset user's paid lessons left when the month ends.
    • Internal admin panel
    • Add several billing address to users
    • Testing
    • Teacher’s blog
    • Check overlap hours in lessons reservation and creation
    • Forgot password functio
    • Check manually wire payment (and prevent lessons to be added before the invoice is mark as paid)
    • Error messages on teacher's lessons scheduling
    • Responsive views (my_calendar)
    • Fix invoice's logo

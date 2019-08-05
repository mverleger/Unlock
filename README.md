# Unlock
UNLOCK - Unique Non-Linear Organizer for Conveying Knowledge

UNLOCK is a non-linear gamification UI for the Canvas LMS system.  Instead of using the vertical "modules" system or manually creating a rectangular page, UNLOCK provides a hexagon-based layout that encourages non-linear curricular organization. The toolset implements multiple gamification elements, specifically
- Individual and Team leaderboards based on Canvas Groups
- A Progress Bar
- Multiple "Levels"
- A non-linear UI with level locking
- Rubric based outcomes reporting

UNLOCK integrates with Canvas in two ways:
1) Using Developer Keys to allow for Canvas API access
2) As an LTI tool for launching an authenticated user

# Installation

Setup Pre-requisites:
- Web Server
- MySQL Database
- Canvas LMS

Once the web-server is setup, update the config.php file to reflect the server location.
For the database, the "Setup" folder contains UNLOCK.sql which creates the tables.
Once the database is setup, the /Setup/LMS_Preconnection_Setup.php creates the LMS access in the database.
In Canvas, create a developer key (contact your Canvas admin) and update config.php.
Then, add the tool through the LTI tools settings.  The Setup/ToolSettings.xml file contains the xml settings for adding the LTI tool.  Update it to reflect the server location.
Finally, create a page in Canvas.  On that page, add the tool as an external tool.


# References
UNLOCK is built on the following tools:
- https://github.com/IMSGlobal/LTI-Tool-Provider-Library-PHP
  - Updated to replace the "mysql" data connector with the "mysqli" data connector.
- https://github.com/cesbrandt/canvas-php-curl
  - Handles the API interfacing

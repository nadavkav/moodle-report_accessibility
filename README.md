# Moodle accessibility report
Generate a content accessibility report, using AXE JS accessibility (ally) HTML checker about course front page content.

# Install instructions:
0. Download the zip file or get it via git.
1. Copy the moodle-report_accessibility directory to the report directory of your Moodle instance.
2. Change its name to "accessibility"
3. Visit the notifications page.

# Accessing the report 
The report can be accessed via the course reports (Course admin > Reports > Accessibility report). Access is controlled by the user context, and the report is currently intended for teachers.

# Future ideas...
* Use an observer to watch over new content that was added, analyse it, and send a report to the relevant user, to fix it, if needed.
* Use NodeJS to do all the processing on the server side
* Send content to an Accessibility web service, and get a report (json). all server side.

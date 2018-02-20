=== Ninja Forms - SalesForce CRM===

== Changelog ==

= 3.0.6 =
2017.10.26

Remove integer 0 from boolean data handling array and specifically set true value

= 3.0.5 =
2017.10.20
Correct typo in special instructions
Special instructions option to preserve ampersand and quote marks
Scrub action settings to remove field option dropdown values during save


= 3.0.4 =
2017.09.23
Add boolean for data handling

= 3.0.3 =
2017.05.19
Add Campaign Linking
Remove HTML tags that appear in text areas
Remove builder template action hooks
Use htmlentities for settings display - avoid html output from response


= 3.0.2 =
2017.04.14
Add status message for Salesforce 403  Forbidden error
This is for Salesforce accounts which do not have API access enabled

= 3.0.1 =
2017.03.20
Change slug and name constants for auto update

= 3.0 =
Add handling for extracting file from file upload in NF3

Add file upload special instructions

Move duplicate check array to shared functions for NF3 use

Correct Field Map Upgrade lookup

Upgrade to NF3


= 1.2.2 = 
Add date formatting function so that form designer can use local date format
on form and date will converted to Salesforce-required date format prior
to submisssion

= 1.2.1 =
Fix is_array code in validate_raw_form_value method in build request

= 1.2 =
Enable Sandbox mode by way of a filter
Correct duplicate filter name for child object array modifications
Add support for file uploads into Salesforce
Add json request to communication details when Salesforce rejects the request


= 1.1.1 =
2015.05.12
Add status update when duplicate check is not requested; before, if no
duplicate check is performed, status does not update.  This change will
enhance support

= 1.1 =
2015.03.23
Add feature - check for duplicate values and create task to validate duplication


= 1.0.7 =
2015.01.22
Check for available fields before refreshing object;
Before would throw a warning if settings were unable to
retrieve an object.  After checks for null and sets
a descriptive error

= 1.0.5 =
2015.01.19
Add automatic connection of Note to Lead, Contact, Account

= 1.0 =
Begin
# Power BI Jira Connector

## Description

The purpose of this connector is to access Jira data from Power BI so you can create more advanced reports.

This connector can also be used to extract data from Jira with any other purpose, i.e. you can extract data to load it into a database.

## Instructions

1. Copy files to your server so you can access it via url like:
https://yourdomain.com/Power-BI-Jira-Connector/index.php
2. Edit tokens in auth function to restrict access to this connector giving authorization only to valid tokens
3. Start your Power BI report using our template available at:
https://github.com/audoxcl/Power-BI-Examples/blob/main/Jira.pbix

In Power BI Desktop you should set all these parameters (in the Power Query Editor window):

1. **url:** the url where the connector is installed.
2. **token:** the token used to use the connector. See auth function to change the way this token is validated. The token 'FREETOKEN' will work until you edit auth function. Also, you can add multiple tokens in auth function.
3. **domain:** the Jira instance sub domain.
4. **email:** the user email to access Jira data.
5. **api_token:** the user token to access Jira data. To get api_token go to: Manage account -> Security -> API tokens -> Create and manage API tokens

This connector might be limited due to Jira API rate limitations.

## Contact Us:

- info@audox.com
- www.audox.com
- www.audox.mx
- www.audox.es
- www.audox.cl

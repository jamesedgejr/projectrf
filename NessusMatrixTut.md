#Nessus Matrix Tutorial

# Introduction #

Users will find the Nessus Matrix helpful in quickly identifying the what hosts have which vulnerabiliteis vulnerabilities.  Creating follow-up matrices will be able to highlight improvements.


## Create the Matrix ##

Select _Nessus Vuln Matrix_ and the Nessus report you want to use.

![http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.1.png](http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.1.png)

Select the hosts and Nessus Plugin Families you want to include in the report.  Select whether you want Critical, High, Medium, Low, or Info or any combination of the five.  You can include the Nessus Plugin Name and/or Family.  You can choose whether you want the hosts listed along the left (vulnerabilities listed along the top) or swap them with vulnerabilities along the left side (hosts along the top).

![http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.2.png](http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.2.png)

Download and open the Comma Delimited (CSV) file.

![http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.3.png](http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.3.png)


## Format the Matrix ##

Opening the raw CSV in Microsoft Excel shows the hosts along the left with the hostname and operating system (if Nessus identified those fields).  Along the tops you see the Plugin Name and Family (if you selected them) and then the first letter of the Risk Factor with the CVSS Base Score in parenthesis.

![http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.4.png](http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.4.png)

The screenshot below highlights what I do in Microsoft Excel to format the raw CSV file when the hosts are listed along the left side.

![http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.5.png](http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.5.png)

The screenshot below highlights what I do in Microsoft Excel to format the raw CSV file when the hosts are listed along the top.

![http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.6.png](http://www.jedge.com/images/projectRF.wiki/matrix/matrix.tutorial.6.png)
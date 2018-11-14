## Preface
- Please note that throughout the code the `Services as general "umbrella" categories ` are refered 
to as `Service Categories` and sometimes just `Categories`.
- `Subservices` are refered to as `Services`.
- - [x] Completed deliverables are marked with a tick.

### Installing the application
___
- You can find a demo version here [http://104.248.249.221/](http://104.248.249.221/). Feel free to test / break the demo app.

* #### Local Instalation
    Download the source code, composer
    ```sh
       composer install     #install the dependencies
     ```  
    ```sh
       bin/console d:d:c     #create the database (bin/console d:d:d --force #to drop the database)
     ```  
    ```sh
       bin/console d:s:c     #create the schema (bin/console d:s:d --force #to drop the schema)
     ```  
    ```sh
       bin/console h:f:l     #optional: upload fixtures [notice: deletes current data]
     ```  
  
     ![fixtures](https://github.com/gliderShip/bwss/blob/master/web/fixtures.png)



BWSS Test project - Offer and Invoice tool
==========================================

Please read this document completely before beginning work.

This document consists of requirements for an Offer and Invoicing tool.

The invoicing tool will have to be built as a web application, running on either Apache or Nginx (your choice).
The database used will be MySql.
The backend technology will be PHP version 5.6 or 7 (your choice).
The frontend can have as much or as little Javascript as you desire. Feel free to use AJAX to improve the interactions where you deem appropriate. Also, feel free to use any Javascript libraries or frameworks you are comfortable with.
Use a css framework of your choice (Bootstrap is fine) or write your own css. The only requirement regarding css is that it's clear and presentable.

Do not use a framework. The point of this excercise is to see how well you understand the http request/response process.
It is ok (and encouraged) to use composer packages and composer autoloading, as long as you are not infringing on the previous point. 

The business requirements are divided into levels. 

It is required to first write the software that meets the requirements for level 1,
then proceed to modify it to fulfill the requirements for level 2, and then level 3.

After finishing each level, make a copy of the code and database for that level. Please present these along with the finished project.

A level's requirement may change or overwrite the requirements from a previous level.
This is to simulate the Agile methodology, where clients are able to shape the application to suit their needs as development continues.

Level 1
=======

The client company wants to have an offering and invoicing tool for their services.
This tool will allow defining services, subservices and billable items, 
creating offer documents containing one or more of these items, and turning them to invoices once approved.

Services
--------

- Services are general "umbrella" categories for the different kinds of services offered by the company.
- Each Service has a name that defines it. This can be anything and is there for display purposes only.

Subservices
-----------

- Each service can have subservices under itself.
- One subservice can only belong under one service
- Subservices have a name that define them. This can be anything and is there for display purposes only.

Costitems
---------

- A costitem is a billable item, used to compose an offer.
- A costitem belongs to a subservice.
- A subservice can have zero or more costitems.
- A costitem is defined by a name, again this is for display purposes only.
- A costitem has a price, this can be either a single amount, or an hourly amount.
- All prices are in EUR.
- A costitem is VAT-inclusive, meaning that VAT-tax is included in the price. The VAT percentage is 21.0%. If the costitem price is 100.00 EUR, this means that the VAT for that costitem is 21.00 EUR.

Offers
------

- An offer is a document (data collection) that includes a single subservice.
- For the selected subservice, all of the costitems belonging to the subservice must be present in the offer.
- For costitems with hourly rating, a textbox beside each item will allow for inputting the number of hours worked on that costitem.
- An offer will contain the subtotal (total sum of all costitems), a VAT total: (the sum of all VAT amounts for each costitem), and a grand total (in this case, the same as the subtotal)
- An offer has a creation date and time.


Deliverables for Level 1:
-------------------------

- [x] Provide a way to define a service.
- [x] Provide a way to define a subservice and assign it to an existing service.
- [x] Provide a way to define a costitem and assign it to a subservice.
- [x] Provide a view for all defined services.
- [x] Provide a view for all defined subservices of that service (you may group them within the same view with the services if you so desire)
- [x] Provide a view for all defined costitems for a selected subservice (you may group them in the same view with the subservice)
- [x] Provide functionality to edit and delete costitems.
- [x] Provide functionality to edit and delete subservices. When deleted, all costitems belonging to that subservice get deleted as well.
- [x] Provide functionality to edit and delete a service. When deleted, all subservices and respective costitems get deleted as well.
- [x] Provide a multi-step form to create an offer.
- Step 1: 
- [x] Chose the service and subservice for that offer.
- [x] Have a "next" button taking you to the next step.
- Step 2: 
- [x] For each of the costitems in the selected subservice that has hourly billing, include a text box where you can enter the number of hours.
- [x] Have a "next" button taking you to the next step.
- Step 3: 
- [x] Display an cverview containing the selected service, subservice and costitems.
- [x] For fixed-price costitems display the cost in EUR beside each costitem
- [x] For hourly-rated costitems, display the price per hour, number of hours
- [x] Display a section called "Totals", containing the subtotal, VAT amount and grand total in EUR.
- [x] Have a "save" button to finish the process.
- [x] The creation date and time of the offer is set automatically upon pressing the "save" button.
- [x] At the end of step 3, save this offer in the database, in a way that the contained service, subservice, and costitem specifications are a copy of the originals and do not reference the originals. This will allow the service/subservice/costitems specificaitons to change in the future without impacting the previous offers.

- [x] Provide a view of all saved offers.
- [x] Provide a way to delete an offer.
- [x] Provide a migration script (either in PHP or SQL) to create the neccessary database tables.

Level 2
=======

Extras
------

- An extra is similar to a costitem, but it belongs to a service
- An extra has a name. This can be anything as is used for display purposes only.
- An extra has a price. The price is of a fixed amount type only.
- All extras are VAT-inclusive.

Costitems
---------

- Some costitems are VAT-exclusive. There needs to be a boolean setting to specify this. If a costitem is VAT-exclusive, its price will not reflect in the VAT amount in the final offer calculations.
- Costitems may now receive discounts by default. There needs to be a boolean setting to specify this.

Discounts
---------

- A discount can be applied to one or more costitems.
- A discount has a fixed value in EUR.
- A discount has a name, for display purposes only.
- A discount has a code of 6 alpha-numeric uppercase characters.
- A discount has a validity time period, therefore a start date and an end date. It can only be applied if the offer being made is within this time period.

Deliverables for Level 2
------------------------

- Provide a way to defina an extra and add it to a service
- Provide a way to list extras based on a service (you may group them with the services view if you so desire)
- Provide a way to edit or delete extras.
- When a service is deleted, all extras belonging to it are also deleted.
- When creating or editing a costitem, add the option to specify whether the price for this costitem also includes VAT (default to yes)
- When creating or editing a costitem, add the option to specify whether this subservice can have discounts applied to it (default to yes)
- Provide a way to create a discount. This should include a way to select one or more costitems that this discount is applicable to.
- Make the following changes to the multi-step offer form:
    - Step 1: 
     - Display extras available for the selected service.
     - Allow selection of zero or more extras.
    - Step 2: 
     - For all costitems that have available discounts, add a dropdown menu listing all eligible discounts (available to this costitem and is within the discounts' valid time period ).
     - Only one discount can be selected per costitem.
     - For each costitem, allow the option to not apply the discount.
     - Add a "back" button taking you to step 1.
    - Step 3: 
     - For each costitem, if a discount is applied, display that discount code and value beside the respective costitem.
     - Below the costitems, display a list of all the extras selected in Step 1, alongside their respective prices.
     - In the subtotal, and total include the sums of the selected extras.
     - In the VAT calculation, include the VAT only for the costitems and additional services that are VAT-inclusive.
     - Below the VAT include the sum of all discounts that have been applied.
     - Make sure the saved offers do not reference the extras, but contain or are linked to a copy instead.
     - Add a "back" button taking you to step 2.
- Provide a migration script (either in PHP or SQL) to create the additional database tables, alter the existing tables to fit the new requirements, and populate newly added fields with their default values.


Level 3
=======

Settings
--------
- Provide a way to change the global VAT percentage.

Price Date Ranges
-----------------

- A price date range is defined as a record of start date, end date and price in EUR for that period.
- A price date range belongs to a costitem.

Costitems
---------

- Costitems can now have variable pricing based on date ranges.
- A costitem's price is now calculated based on price date ranges. If the a price range that includes the current date exists for that costitem, the price used is that of the price date range, otherwise the default price value of the costitem is used.
- If multiple date ranges are specified for the same costitem, that include the current date, the last date range entered is used.  

Offers
------

- Offers can now be edited. This is true only for offers that have not yet been sent.
- Offers can now be sent to customers via email after it is saved. (we will simulate this and not send a real email)
- If an offer has been sent to a customer, a boolean attribute needs to be set that indicates so.
- A sent offer cannot be edited or deleted.
- A client can now accept the offer. (we will simulate this manually)
- An accepted offer includes the acceptation date.
- An accepted offer can generate an invoice.

Invoices
--------

- Invoices are "formal" versions of an accepted offer.
- Besides the data from the offer that generates the invoice, an invoice has:
- A issue date
- A valid until date (issue date + 21 days)
- A paid boolean flag
- A paid date
- Just like with offers, the data composing the invoice must not reference the original data, but must make copies of it.

Deliverables for Level 3
------------------------

- Provide a way to change the VAT amount setting

- Provide a way to specify price date ranges for costitems (if you like, you can do this within the edit costitem view)
- Provide a way to modify and delete price date ranges for a costitem

- Provide a view of offers, filterable by their status: draft (not yet sent), sent, and approved.
- For draft offers provide edit and delete and "send" capabilities (the send action will only need to mark the offer as sent and set the appropriate date)
- For sent offers provide an "accepted" action, that will mark the offer as accepted.
- For accepted offers, provide a "make invoice" action that will generate an invoice based on the offer.
- Provide an invoices view with the following filters:
- Pending, for invoices that have been sent but have not been paid (but are within the "valid until" date)
 - invoices within 5 days of expiring must have an indicator at the side that says so.
- Paid, for paid invoices
- Expired, for invoices that have expired without being paid.

- When editing an offer, display the multi-step form, starting at step 3 (the overview). Allow for navigating to the previous steps and making  changes there.


Final notes / tips
------------------

The aim of this excercise is to see how well you can interpret business requirements and implement software specifications.

The end application is not meant to be production-ready. Please do not waste time in things such as authentication or excessive styling.

For repetitive tasks such as validation, please chose one or a few points in the application and implement validation there. As long as the validation logic is correct in that part of the application, you are fine.

If you cannot manage to finish the whole excercise, provide what you have done so far. After a discussion over the code, we will still be able to see your strong points and what skills might need to be improved. For best results try to focus on each level one by one, without worrying too much about the next levels.
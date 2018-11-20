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
    ```sh
       bin/console server:run     # run built-in web server
     ```  
  
     ![fixtures](https://github.com/gliderShip/bwss/blob/Level_2/web/fixtures.png)




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


Deliverables for Level 2
------------------------

- [x] Provide a way to defina an extra and add it to a service
- [x] Provide a way to list extras based on a service (you may group them with the services view if you so desire)
- [x] Provide a way to edit or delete extras.
- [x] When a service is deleted, all extras belonging to it are also deleted.
- [x] When creating or editing a costitem, add the option to specify whether the price for this costitem also includes VAT (default to yes)
- [x] When creating or editing a costitem, add the option to specify whether this subservice can have discounts applied to it (default to yes)
- [x] Provide a way to create a discount. This should include a way to select one or more costitems that this discount is applicable to.
- Make the following changes to the multi-step offer form:
   - [x] Step 1: 
     - [x] Display extras available for the selected service.
     - [x] Allow selection of zero or more extras.
   - Step 2: 
     - [x] For all costitems that have available discounts, add a dropdown menu listing all eligible discounts (available to this costitem and is within the discounts' valid time period ).
     - [x] Only one discount can be selected per costitem.
     - [x] For each costitem, allow the option to not apply the discount.
     - Add a "back" button taking you to step 1.
   - Step 3: 
     - [x] For each costitem, if a discount is applied, display that discount code and value beside the respective costitem.
     - [x] Below the costitems, display a list of all the extras selected in Step 1, alongside their respective prices.
     - [x] In the subtotal, and total include the sums of the selected extras.
     - [x] In the VAT calculation, include the VAT only for the costitems and additional services that are VAT-inclusive.
     - Below the VAT include the sum of all discounts that have been applied.
     - [x] Make sure the saved offers do not reference the extras, but contain or are linked to a copy instead.
     - Add a "back" button taking you to step 2.
- [x] Provide a migration script (either in PHP or SQL) to create the additional database tables, alter the existing tables to fit the new requirements, and populate newly added fields with their default values.

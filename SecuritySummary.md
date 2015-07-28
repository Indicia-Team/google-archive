Security provided by Indicia is for several reasons:
  1. To authenticate Indicia administrators when they access the Core Module's GUI.
  1. To authenticate online recording website administrators when they access the Core Module's GUI (e.g. to define your controlled vocabularies).
  1. To authenticate website users who are logged into an online recording website so that their records may be linked to their user entry.
  1. Most importantly, to authenticate the websites themselves which are able to use the services provided by the core module, to prevent malicious use of the services.

So, if the online recording website allows members of the public to access a data entry form without first creating a registration, then the website code will still be expected to prove that it is from the website before it can access the services to post a record. We will be using a form of digest authentication to do this and will provide guidance and sample PHP code.

If the online recording website is going to require users to log in, then Indicia will also expect the website to create a Person record when you register the user's account, via the data services, and keep a link to the Person that has been created. This will allow the website to submit data that is linked to that Person when they are logged in.
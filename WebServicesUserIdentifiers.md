# User Identifier Services #

**Introduced in Indicia 0.8**

The User Identifier Services provide a way for client websites to get a unique identifier (a warehouse User ID) for any user of the website. Because this identifier is associated with the user's external identifiers, such as twitter accounts or Open IDs, one user logging into several client websites linked to the same warehouse can end up with just 1 warehouse user ID. This makes it simple to identify a user's records globally for reporting and other purposes.

## How it works ##

Each client website has one or more **identifiers** for the users that log in. This must at least include an email address, but any additional types of identifiers such as twitter, facebook or OpenID URLs can also be used. The client website should also store the surname and preferably the user's firstname in the user account profile. When the user logs in to the client website or their user account profile is updated, the client website should send a request to the User Identifier Services to retrieve a warehouse User ID for the user. This request should include a list of all the user's known identifiers as well as the user's surname and firstname if available. It can optionally include any additional attributes about the user that the client website would like to synchronise with the warehouse, for example the user's interests from their user profile. The **Easy Login** Instant Indicia feature includes support for all the required client-side functionality.

The warehouse uses the supplied identifier list to look for a user that has at least some overlap with the identifiers supplied. If found, then the user ID is returned. If not, then the surname and first name are used to create a new warehouse person and user. The user is always added to the list of known users on the warehouse for the website which made the call, whether existing or new. The list of identifiers are stored on the warehouse for future use. The warehouse also updates any synchronised attributes and returns updated attribute values that should be saved into the client website user profile.

If a situation arises where 2 apparently different users exist on a warehouse, then a request for a user ID is received which has a list of identifiers which overlaps the 2 users, then it is likely that these users are all the same person. The resolution of this is not automated as it would be too prone to error, though tools to assist resolution will be provided on the warehouse. The "best fit" user ID is returned, based on an exact name match first, then the number of identifiers which overlap.

## Service Details ##

The service is called at address **index.php/services/user\_identifier/get\_user\_id**.

The service call requires the following parameters in the GET or POST data:

| **nonce** | Write authentication nonce token |
|:----------|:---------------------------------|
| **auth\_token** || Write authentication auth token ||
| **identifiers** | Required. A JSON encoded array of identifiers known for the user. Each array entry is an object with a type property (e.g. twitter, openid) and identifier property (e.g. twitter account), e.g. type=twitter and identifier=mytwitteraccount. An identifier of type email must be provided in case a new user account has to be created on the warehouse. |
| **surname** | Required. Surname of the user, enabling a new user account to be created on the warehouse. |
| **first\_name** | Optional. First name of the user, enabling a new user account to be created on the warehouse. |
| **cms\_user\_id** | Required. User ID from the client website's login system. |
| **force** | Optional. Only relevant after a request has returned an array of several possible matches. Set to merge or split to define the action. |
| **users\_to\_merge** | If force=merge, then this parameter can be optionally used to limit the list of users in the merge operation. Pass a JSON encoded array of user IDs.|
| **attribute\_values** | Optional list of custom attribute values for the person which have been modified on the client website and should be synchronised into the warehouse person record. The custom attributes must already exist on the warehouse and have a matching caption, as well as being marked as synchronisable or the attribute values will be ignored. Provide this as a JSON object with the properties being the caption of the attribute and the values being the values to change. |

The service call returns a JSON string containing:

**userId** - If a single user account has been identified then returns the Indicia user ID for the existing or newly created account. Otherwise not returned.

**attrs** - If a single user account has been identifed then returns a list of captions and values for the attributes to update on the client account.

**possibleMatches** - If a list of possible users has been identified then this property includes a list of people that match from the warehouse - each with the user ID, website ID and website title they are members of. If this happens then the client must ask the user to confirm that they are the same person as the users of this website and if so, the response is sent back with a force=merge parameter to force the merge of the people. If they are the same person as only some of the other users, then use users\_to\_merge to supply an array of the user IDs that should be merged. Alternatively, if force=split is passed through then the best fit user ID is returned and no merge operation occurs.

**error** - Error string if an error occurred.

## Database Details ##

The list of identifiers for a user are stored in the **user\_identifiers** table.
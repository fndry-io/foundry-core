# Foundry Core

# Standards

The following outline the standards for sepcific parts and class objects within the Foundry Framework.

## Repositories, Models and/or Entities

The objective with the Foundry Framework is to get the data layer and anything dealing with working with the data 
abstracted from the rest of the framework. Similar to a black box and allowing the data store to be changed as needed.

The goal is also to abstract the ORM layer so that the exact model it uses can also be switched at any time.

The following rules and guidelines apply to Repositories:

 - They MUST ONLY access the data store, no other class outside of the repository and Model/Entity may access the store.
 - They MUST ONLY expect objects interface of object they receive. 
 - They MUST NOT expect a specific Model class, ONLY an interface or the base Model class. I.E. Model|IsPerson as an 
   example is allowed, but Person (A specific Model) is not allowed.
 - They SHOULD return boolean's, arrays or collections of the Models they work with
 - They can throw exceptions.
 
## Services

The objective of Services is to abstract the business layer of the system and allow for Services to be called by other
Services, and all services return a known structure.

The following rules and guidelines apply to Services:

 - All methods MUST NOT expect a specific Model class, ONLY an interface. I.E. Model|IsPerson as an example is allowed, 
   but Person (A specific Model) is not allowed.
 - All methods MUST return an instance of the Response object. 
 - Inputs, or input collections must extend the base Inputs class.
 - Provided Inputs MUST have already been validated.
 - Services SHOULD only need to receive ORM object instances and Input classes.
 - Services MUST use repository classes to access and mutate data. They MUST not manipulate Model/Entity objects directly.

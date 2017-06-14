# ISAC-API Example (PHP)
This project describes how to use the ISAC-API in your business processes with PHP.

The following actions are currently implemented:
- Getting all resellercloud instances
- Resizing an instance
- Creating a firewall rule for an instance

The complete API description can be found under:[https://doc.isac.de](https://doc.isac.de "API Documentation")


## Example
    
    $isacConnector = new connectAPI();

    // Authenticating against the API
    $isacConnector->authenticate();

    // Getting the ResellerCloud Instances
    $instances = $isacConnector->getInstances();
 


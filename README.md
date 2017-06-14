# ISAC-API Example (PHP)
This project desribes how to use the ISAC-API in your business processes with PHP.

The following actions are currently implemented:
- Getting all resellercloud instances
- Resizing an instance
- Creating a firewall rule for an instance

## Example
    
    $isacConnector = new connectAPI();

    // Authenticating against the API
    $isacConnector->authenticate();

    // Getting the ResellerCloud Instances
    $instances = $isacConnector->getInstances();
 


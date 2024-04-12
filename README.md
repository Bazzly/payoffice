# Payoffice
## All payment system in one

# This package is to solve just one problem 
## Api url can be down this means payment on website will not go through
## this package will allow user or admin to ping and see 
## the status of every fintech API url before accepting them 
## to be the use as the prefered payment option.



## Default preset ping is 10ms user can decide to increase it when they are experiencing large payment
## companyName,
## APIUrl 
## prefered ping

## $data = new PingServer(string $name, string $url, int $preferSeverPing = null);
# Output result data
``'companyName'``
``'APIUrl'``
``'serverStatus'``
``'serverPing'``
``'userPing'``
## Credit to all this people and there previous work that make payment office easier to build using there already well written payment api.

## unicodeveloper ['https://github.com/unicodeveloper/laravel-paystack']
# Demo solution PHP backend

## How to run

Setup composer.json as instructed in Demo company SDK portal or create `auth.json` file with Demo company details
```
{
  "http-basic": {
    "sdk.uniscale.com": {
      "username": "usernameHere",
      "password": "passwordHere"
    }
  }
}
```

Then run composer update to fetch all your dependencies.

After successfully updating all dependencies, you can:
- Run in IDE of choice
- or in command line at project root:
    - `php account.php`
    - `php messages.php`

These will run each service on their own servers.

## How to use

Send backend action request to `/api/service-to-module/{featureId}`. Port 5298 for account service and port 5192 for messages.
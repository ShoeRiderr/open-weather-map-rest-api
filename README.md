### OpenWheatherMap RestAPI

This is the basic app for fetching basic information about the current weather in a given sent localization.

#### Routes (method - endpoint > controller method)
- POST - api/login › Api\AuthController@login
  Params (param - validation)
  * email - required|email
  * password - required
- POST - api/logout › Api\AuthController@logout
  Bearer token required for successful logout
- POST - api/register › Api\AuthController@register
  Params (param - validation)
  * name - required
  * surname - required
  * email - required|email|unique,users,email
  * password - required|confirmed
- DELETE - api/users/{user} › Api\UserController@destroy
  Access to delete only logged in user
- GET|HEAD - api/weather/current › Api\OpenWheatherMapController@getCurrentWheather
  ##### Params (param - validation)
  * lat - required
  * lon - required
  * exclude nullable|string
  * units nullable|string
  * lang nullable|string
  ##### Response example
  ```
  {
    "temp": 299.48,
    "pressure": 1014,
    "humidity": 82,
    "clouds": 0,
    "wind_speed": 1.34,
    "wind_deg": 100
  }
  ```
- GET|HEAD - sanctum/csrf-cookie
  

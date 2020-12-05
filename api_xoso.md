### API truyen88.net
- Endpoints:

	- Pre-endpoint: **api = "http://192.168.1.111/xoso/public/api"**
	- Danh sách tỉnh:
		_GET /province

- Detail for endpoint:

  - **Danh sách tỉnh**:

    - GET /province
    
    - Responses:

      	- OK:

        	- Status Code: true
        	- Payload:
          	```
          	{
              "status": true,
              "provinces": {
                  "id": "int",
                  "name": "string",
              }
          	}
          	```

      	- Bad request"
        	- Status Code: false
        	- Payload:
          	```
          	{
              "status": false,
              "message": "error",
          	}
          	```
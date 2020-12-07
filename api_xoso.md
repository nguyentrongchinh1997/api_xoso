### API truyen88.net
- Endpoints:

	- Pre-endpoint: **api = "http://192.168.1.111/xoso/public/api"**
	- Danh sách tỉnh:
		_GET /province
	- Kết quả đề theo miền, tỉnh
		_GET /result-lottery?province_id={number}&date={d-m-Y}
	- Kết quả xổ số điện toán theo ngày
		_GET /xsdt?date=06-12-2020

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
  - **Kết quả đề theo tỉnh, miền**

	- GET /result-lottery?province_id={number}&date=06-12-2020

	- Chú ý: province_id = -1 nếu là miền bắc, ngược lại là các id của tỉnh đã gửi trong api /province
    
    - Responses:

      	- OK:

        	- Status Code: true
        	- Payload:
          	```
          	{
              "status": true,
              "data": {
                  "id": "int",
                  "gdb": "string"
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

  - **Kết quả xổ số điện toán**

	- GET /xsdt?date=06-12-2020

    - Responses:

      	- OK:

        	- Status Code: true
        	- Payload:
          	```
          	{
              "status": true,
              "data": {
                  "dt123": "string",
                  "dt6x36": "string"
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
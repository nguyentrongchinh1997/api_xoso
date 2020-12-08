### API truyen88.net
- Endpoints:

	- Pre-endpoint: **api = "http://192.168.1.111/xoso/public/api"**
	- Danh sách tỉnh:
		_GET /province
	- Kết quả đề theo miền, tỉnh
		_GET /result-lottery?province_id={number}&date={d-m-Y}
	- Kết quả xổ số điện toán theo ngày
		_GET /xsdt?date=06-12-2020
	- Danh sách các loại Vietlott
		_GET /vietlott
	- Kết quả vietlott theo loại và ngày
		_GET /result-vietlott?vietlott_id={id}&date={Y-m-d}
	- Danh sách 3 miền
		_GET /region
	- Kết quả tìm kiếm theo miền và ngày
		_GET /result-region?region_id={id}&date{d-m-Y}
	- Kết quả logan theo ngày và miền
		_GET /logan?region_id={id}&date={Y-m-d}
	- Thống kê loto từ 00-99
		_GET /loto0099/{numberDay}/{regionId}

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

  - **Danh sách loại Vietlott**

	- GET /vietlott

    - Responses:

      	- OK:

        	- Status Code: true
        	- Payload:
          	```
          	{
              "status": true,
              "data": {
                  "id": "int",
                  "name": "string"
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

  - **Danh sách các miền**

	- GET /region

    - Responses:

      	- OK:

        	- Status Code: true
        	- Payload:
          	```
          	{
              "status": true,
              "data": {
                  "id": "int",
                  "name": "string"
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

  - **Kết quả xổ số theo ngày và miền**

	- GET /result-region?region_id={region_id}&date=01-12-2020

	- Chú ý: region_id là id miền trả về từ api /region

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
				  ....
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
			  
  - **Kết quả vietlott theo loại và ngày**

	- GET /result-vietlott?vietlott_id={id}&date={Y-m-d}

	- Chú ý: vietlott_id là id loại vietlott trả về từ api /vietlott

    - Responses:

      	- OK:

        	- Status Code: true
        	- Payload:
          	```
          	{
              "status": true,
              "data": {
                  "id": "int",
                  "vietlott_id": "string"
				  "number": "string"
				  ....
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

  - **Kết quả logan**

	- GET /logan?region_id={id}&date={Y-m-d}

	- Chú ý: region_id=-1 là miền bắc, các tỉnh lẻ lấy id từ api /province

    - Responses:

      	- OK:

        	- Status Code: true
        	- Payload:
          	```
          	{
              "status": true,
              "data": {
                  "lô": "số lần chưa ra",
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

  - **Thống kê loto 00-99**

	- GET /loto0099/{numberDay}/{regionId} => /loto0099/10/18

	- Chú ý: numberDay là số lần quay gần nhất, ví dụ numberDay = 10,20,....; regionId = -1 miền Bắc, các tỉnh lẻ là gửi id từ api /province

    - Responses:

      	- OK:

        	- Status Code: true
        	- Payload:
          	```
          	{
              "status": true,
              "data": {
                  "lô": "số lần ra",
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
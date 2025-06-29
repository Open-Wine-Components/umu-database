PHP Server host requirements:
```
apt install php-mysqlnd
apt install php-curl php-dom php-gd php-imagick php-zip php-intl
```

PHP query example:
```
<?php
$url = "https://umu.openwinecomponents.org/umu_api.php?codename=codename&store=store";
$response = file_get_contents($url);
$data = json_decode($response, true);

foreach ($data as $item) {
    echo $item['umu_id'];
}
?>
```
PHP query example using curl:
```
<?php
$url = "https://umu.openwinecomponents.org/umu_api.php?codename=codename&store=store";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

foreach ($data as $item) {
    echo $item['umu_id'];
}
?>
```

Python query requirements:
`pip install requests`

Python query example:
```
import requests

# Define the base URL of the API endpoint
base_url = "https://umu.openwinecomponents.org/umu_api.php"

# Define the parameters for the GET request
params = {"codename": "codename", "store": "store"}

# Send the GET request
response = requests.get(base_url, params=params)

# Parse the JSON response
data = response.json()

# Print the data
for item in data:
    print(f"Title: {item['title']}, UMU ID: {item['umu_id']}")
```

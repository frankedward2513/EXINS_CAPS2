import requests

data = {

    "Category":"Shirts",

    "Year":2026,

    "Month":7,

    "Day":22,

    "DayOfWeek":2,

    "Lag1":5200,

    "Lag7":4800,

    "Rolling7":5000

}

response = requests.post(
    "http://127.0.0.1:5000/predict",
    json=data
)

print("Status Code:", response.status_code)
print("Response:")
print(response.text)
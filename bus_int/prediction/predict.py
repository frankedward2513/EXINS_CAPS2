import os
import pandas as pd
import joblib
from xgboost import XGBRegressor

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

model_path = os.path.join(BASE_DIR, "sales_forecast_model.json")
encoder_path = os.path.join(BASE_DIR, "category_encoder.pkl")

model = XGBRegressor()
model.load_model(model_path)

encoder = joblib.load(encoder_path)

def predict_sales(data):
    category_input = data.get("Category")
    
    # Check if category exists in encoder classes to avoid unhandled KeyErrors
    if category_input not in encoder.classes_:
        raise ValueError(f"Invalid category '{category_input}'. Choose from: {list(encoder.classes_)}")

    category = encoder.transform([category_input])[0]

    X = pd.DataFrame([{
        "Category": category,
        "Year": data["Year"],
        "Month": data["Month"],
        "Day": data["Day"],
        "DayOfWeek": data["DayOfWeek"],
        "Lag1": data["Lag1"],
        "Lag7": data["Lag7"],
        "Rolling7": data["Rolling7"]
    }])

    prediction = model.predict(X)

    return {"prediction": float(prediction[0])}
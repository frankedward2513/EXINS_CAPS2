from flask import Flask, request, jsonify, render_template
from predict import predict_sales
import os
import pandas as pd
from datetime import datetime

app = Flask(__name__)

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
CSV_PATH = os.path.join(BASE_DIR, "forecast_dataset.csv")

@app.route("/")
def home():
    return jsonify({"status": "Sales Forecast API Running"})

@app.route("/predict", methods=["POST"])
def predict():
    try:
        data = request.get_json()
        if not data:
            return jsonify({"error": "No input data provided"}), 400
            
        result = predict_sales(data)
        return jsonify(result)
        
    except Exception as e:
        return jsonify({"error": str(e)}), 500

@app.route("/forecast-form", methods=["GET", "POST"])
def forecast_form():
    prediction_result = None
    error_message = None

    if request.method == "POST":
        try:
            category = request.form.get("Category")
            date_str = request.form.get("Date") 
            lag1 = float(request.form.get("Lag1", 0))
            lag7 = float(request.form.get("Lag7", 0))
            rolling7 = float(request.form.get("Rolling7", 0))

            dt = datetime.strptime(date_str, "%Y-%m-%d")
            year = dt.year
            month = dt.month
            day = dt.day
            day_of_week = dt.weekday()

            input_data = {
                "Category": category,
                "Year": year,
                "Month": month,
                "Day": day,
                "DayOfWeek": day_of_week,
                "Lag1": lag1,
                "Lag7": lag7,
                "Rolling7": rolling7
            }

            pred_result = predict_sales(input_data)
            predicted_sales = pred_result["prediction"]
            prediction_result = round(predicted_sales, 2)

            new_row = {
                "Category": category,
                "Year": year,
                "Month": month,
                "Day": day,
                "DayOfWeek": day_of_week,
                "Lag1": lag1,
                "Lag7": lag7,
                "Rolling7": rolling7,
                "Sales": predicted_sales
            }

            if os.path.exists(CSV_PATH):
                df = pd.read_csv(CSV_PATH)
                df = pd.concat([df, pd.DataFrame([new_row])], ignore_index=True)
            else:
                df = pd.DataFrame([new_row])
            
            df.to_csv(CSV_PATH, index=False)

        except Exception as e:
            error_message = str(e)

    chart_labels = []
    chart_data = []
    
    if os.path.exists(CSV_PATH):
        df_chart = pd.read_csv(CSV_PATH)
        if {"Year", "Month", "Day", "Sales"}.issubset(df_chart.columns):
            # Create a clean datetime column for accurate sorting
            df_chart['FullDate'] = pd.to_datetime(
                df_chart['Year'].astype(str) + '-' + 
                df_chart['Month'].astype(str).str.zfill(2) + '-' + 
                df_chart['Day'].astype(str).str.zfill(2)
            )
            
            # Sort chronologically by date
            df_chart = df_chart.sort_values(by='FullDate')
            
            # Format back to string for the chart axis labels
            chart_labels = df_chart['FullDate'].dt.strftime('%Y-%m-%d').tolist()
            chart_data = df_chart['Sales'].tolist()

    return render_template(
        "index.html", 
        prediction=prediction_result, 
        error=error_message, 
        chart_labels=chart_labels, 
        chart_data=chart_data
    )

if __name__ == "__main__":
    app.run(debug=True)
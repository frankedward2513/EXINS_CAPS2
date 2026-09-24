from xgboost import XGBRegressor

model = XGBRegressor()
model.load_model("sales_forecast_model.json")

print(model.get_booster().feature_names)
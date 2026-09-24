import joblib

encoder = joblib.load("category_encoder.pkl")

print(type(encoder))
print("Classes:", encoder.classes_)

print("Encode Shirt:", encoder.transform(["Shirt"]))
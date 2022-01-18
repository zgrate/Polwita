from flask import Flask, request
from flask_cors import CORS

import database

app = Flask(__name__)
CORS(app)


@app.route('/')
def hello_world():  # put application's code here
    return 'Hello World!'


@app.route('/get_basket/<id>')
def get_basket(id: int):
    print(id)
    return {"basket": database.get_basket(id)}


@app.route("/get_articles/<comma_separated_ids>")
def get_articles(comma_separated_ids: str):
    return {"articles": database.get_articles(comma_separated_ids.split(","))}


@app.route("/get_user/<id>")
def get_user_endpoint(id: int):
    return {"user": database.get_user(id)}


@app.route("/erecepta")
def get_receipt():
    num = request.args["number"]
    pesel = request.args["pesel"]
    print(num)
    print(pesel)
    return {"erecepta": database.get_e_recepta(num, pesel)}


@app.route("/add_to_basket", methods=["POST"])
def add_to_basket():
    print(request.json["items"])
    database.add_to_basket(request.json["user_id"], request.json["items"])
    return {}


@app.route("/update_get_basket/<user_id>")
def update_get_basket(user_id):
    database.update_basket(int(user_id), int(request.args["article"]), int(request.args["new_amount"]))
    return get_basket(user_id)


@app.route("/delivery_forms")
def delivery_forms():
    return {"deliveries": [{
        "id": 1,
        "name": "PP Paczka 24",
        "price": 10.30,
        "prepaid": False
    }, {
        "id": 2,
        "name": "PP Paczka 48",
        "price": 8.30,
        "prepaid": False
    }, {
        "id": 3,
        "name": "PP Paczka 48 za pobraniem",
        "price": 13.0,
        "prepaid": True
    }, {
        "id": 4,
        "name": "Inpost Paczkomat",
        "price": 8.0,
        "prepaid": False
    }, {
        "id": 5,
        "name": "Inpost Paczkomat Pobranie",
        "price": 11.0,
        "prepaid": True
    }]}


@app.route("/payments/<user_id>")
def get_payment_methods(user_id):
    return {'payments': database.get_payment(user_id)}


if __name__ == '__main__':
    app.run()

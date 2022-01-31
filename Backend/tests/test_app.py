import json

import pytest

from Backend import database
from Backend.app import app
from Backend.classes import Zamowienie


@pytest.fixture
def client():
    database.configure_in_memory_db()
    database.createTables()
    database.fill_with_data()
    with app.test_client() as client:
        yield client


def test_user(client):
    rv = client.get("/get_user/1")
    assert rv.status_code == 200
    assert rv.json is not None
    assert rv.json["user"]["IdK"]

    rv = client.get("/get_user/0")
    assert rv.status_code == 404


def test_basket(client):
    rv = client.get("/get_basket/1")
    assert rv.status
    assert rv.json is not None
    assert len(rv.json["basket"]) == 0


def test_add_basket(client):
    rv = client.post("/add_to_basket", data=json.dumps({
        "user_id": 1,
        "items": [{"id": 0, "amount": 1}, {"id": 1, "amount": 2}]
    }), content_type='application/json')
    assert rv.status_code == 200

    rv2 = client.get("/get_basket/1")
    assert rv2
    assert rv2.status_code == 200
    assert rv2.json is not None
    assert len(rv2.json["basket"]) > 0


def test_update_basket(client):
    rv0 = client.get("/get_basket/1")
    assert rv0.status_code == 200
    assert rv0.json is not None
    a = next((x for x in rv0.json["basket"] if x["id"] == 1), None)
    assert a is not None
    assert a["amount"] == 2

    rv1 = client.get("/update_get_basket/1?article=1&new_amount=10")
    assert rv1.status_code == 200
    assert rv1.json is not None
    a = next((x for x in rv1.json["basket"] if x["id"] == 1), None)
    assert a is not None
    assert a["amount"] == 10

    rv2 = client.get("/get_basket/1")
    assert rv2
    assert rv2.status_code == 200
    assert rv2.json is not None
    a = next((x for x in rv2.json["basket"] if x["id"] == 1), None)
    assert a is not None
    assert a["amount"] == 10


def test_get_prescription(client):
    rv = client.get("/erecepta?number=1111&pesel=71072268951")
    assert rv.status_code == 200
    assert "erecepta" in rv.json


def test_payments(client):
    rv = client.get("/payments/1")
    assert rv.status_code == 200
    assert len(rv.json["payments"]) > 0


def test_order(client):
    rv = client.get("/get_basket/1")
    assert len(rv.json["basket"]) > 0

    deliv = client.get("/delivery_forms")
    assert deliv.status_code == 200
    delivery = deliv.json["deliveries"][0]
    assert delivery is not None

    rv = client.get("/payments/1")
    assert rv.status_code == 200
    payment = rv.json["payments"][0]

    rv2 = client.post("/order/1", content_type='application/json',
                      data=json.dumps({
                          "name": "Imie nazwisko",
                          "address": "Michala Rodakowskiego 24",
                          "postcode": "11-111",
                          "city": "Poznan",
                          "delivery_method": delivery,
                          "payment_method": payment
                      }))
    assert rv2.status_code == 200

    with database.Session() as session:
        assert session.query(Zamowienie).count() > 0

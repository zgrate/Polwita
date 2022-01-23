import pytest

import database
from app import app


@pytest.fixture
def client():
    database.configure_in_memory_db()
    database.createTables()
    with app.test_client() as client:
        yield client


def test_basket(client):
    rv = client.get("/")
    print(rv.status)
    assert rv.status
    assert rv is not None

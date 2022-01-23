import datetime
import os
import random
from typing import Optional

import bcrypt as bcrypt
import sqlalchemy
from faker import Faker
from sqlalchemy import create_engine
from sqlalchemy.future import Engine
from sqlalchemy.orm import sessionmaker

from classes import *

SALT = bcrypt.gensalt()

USERNAME = "polwita"
PASSWORD = "12345678"
HOST = "vps.zgrate.ovh"
PORT = 3306
DB_NAME = "Polwita"

engine: Engine
Session: sessionmaker = sessionmaker()


def createTables():
    Base.metadata.create_all(engine, tables=Base.metadata.tables.values(), checkfirst=True)


def configure_in_memory_db():
    global engine
    engine = create_engine('sqlite:///:memory:', echo=True)
    engine.connect()
    Session.configure(bind=engine)


def configure_remote_db():
    engine = create_engine(f"mysql+mysqldb://{USERNAME}:{PASSWORD}@{HOST}:{PORT}/{DB_NAME}")
    engine.connect()
    Session.configure(bind=engine)
    print("TEST")


baskets = {
    2: {
        51: 1,
        55: 2,
        81: 1
    }
}


def row2dict(row):
    d = {}
    for column in row.__table__.columns:
        d[column.name] = str(getattr(row, column.name))

    return d


def get_basket(id):
    print(baskets.get(int(id)))
    basket = baskets.get(int(id))
    if basket is None:
        return []
    else:
        def tf(key):
            article: Optional[Artykul] = get_article(key)
            return {
                "id": key,
                "article": row2dict(article) if article is not None else "not_found",
                "amount": basket[key]
            }

        return list(map(tf, basket.keys()))


def get_article(id):
    with Session() as session:
        return session.query(Artykul).get(id)


def get_articles(list_ids: list[str]):
    def str_to_id(s):
        try:
            return int(s)
        except ValueError:
            return 0

    int_ids = list(filter(lambda d: d != 0, map(str_to_id, list_ids)))

    with Session() as session:
        query = session.query(Artykul)

        return list(map(lambda d: row2dict(d), query.filter(Artykul.IdT.in_(int_ids)).all()))


def get_user(id):
    with Session() as session:
        user = session.query(Uzytkownik).get(id)
        d = row2dict(user) if user is not None else {}
        d.pop("Haslo")
        d.pop("Pesel")
        return d


def fill_with_data():
    fake = Faker(locale="pl_PL")
    print(dir(fake))
    session = Session()

    session.begin()
    print(session.query(Uzytkownik).count())
    if session.query(Uzytkownik).count() < 2:
        for i in range(10):
            u = Uzytkownik()
            fname: str = fake.name()
            name = fname.split(" ")
            u.Imie = name[0]
            u.Nazwisko = name[1]
            u.Adres = fake.address()
            u.Email = fake.email()
            u.DataUrodzenia = fake.date_of_birth()
            u.IdRU = 1
            u.Login = fname.replace(" ", "") + str(random.randint(0, 1000))
            u.Pesel = fake.pesel()
            u.Haslo = bcrypt.hashpw("1234".encode("utf-8"), SALT)
            session.add(u)

    session.commit()
    os.environ.get("DISCORD_TOKEN")
    if session.query(Artykul).count() == 0:
        for i in range(50):
            a = Artykul()
            a.Nazwa = fake.word() + " " + fake.word() + " " + str(random.randint(0, 100)) + "mg"
            a.Cena = random.randint(500, 5000) / 100
            a.CzyRecepta = fake.boolean(chance_of_getting_true=30)
            a.LiczbaSzt = random.randint(0, 100)
            a.KodKreskowy = str(random.randint(10000, 999999999))
            session.add(a)

    session.commit()
    session.close()
    print(session.query(Uzytkownik).all())


def get_e_recepta(numer, pesel):
    # connect_to_nfz()
    # fetch()
    # close()
    if numer == '1111':
        # [
        #     {
        #         'item': row2dict(get_article(53)),
        #         'amount': 1
        #     },
        #     {
        #         'item': row2dict(get_article(55)),
        #         'amount': 2
        #     },
        #     {
        #         'item':  row2dict(get_article(58)),
        #         'amount': 1
        #     }
        # ]
        return {
            53: 1, 55: 2, 58: 1
        }

    else:
        return {}


def add_to_basket(user_id: str, items: list):
    basket = baskets[int(user_id)]
    print(items)
    for dic in items:
        amount = 0 if int(dic["id"]) not in basket else basket[int(dic["id"])]
        basket[int(dic["id"])] = amount + int(dic["amount"])


def update_basket(user_id, article_id, new_amount):
    basket = baskets[user_id]
    if new_amount > 0:
        basket[article_id] = new_amount
    else:
        del basket[article_id]


def get_payment(user_id):
    return [{
        'id': 1,
        'type': 'card',
        'four-digits': 1111

    }, {
        'id': 2,
        'type': 'other',
        'name': 'Przelewy24'
    }]


def order(json: dict, user_id):
    # ["name", "address", "postcode", "city", "delivery_method", "payment_method"]
    session: sqlalchemy.orm.Session
    with Session() as session:
        session.begin()
        zam = Zamowienie()

        zam.DataZlozenia = datetime.datetime.now()
        zam.UzytkownicyIdK = user_id
        zam.StatusZamowienia = 1
        zam.IdZ = 0
        check: sqlalchemy.orm.Session
        with Session() as check:
            while zam.IdZ == 0:
                i = random.randint(0, 0xffffff)
                if check.query(Zamowienie).get(i) is None:
                    zam.IdZ = i

        def toOBM(d: dict):
            artykulWZam = ArtykulWZamowieniu()
            artykulWZam.TowaryIdT = d["id"]
            artykulWZam.LiczbaSzt = d["amount"]
            artykulWZam.CenaWZamowieniu = d["article"]["Cena"]
            artykulWZam.ZamowieniaIdZ = zam.IdZ
            return artykulWZam

        dostawa = Dostawa()
        dostawa.ZamowienieIdZ = zam.IdZ
        dostawa.AdresDostawy = json['address'] + " " + json["city"] + " " + json["postcode"]
        dostawa.SposobDostawy = json["delivery_method"]["name"]
        dostawa.CenaDostawy = int(float(json["delivery_method"]["price"]) * 100)
        dostawa.PlatnoscPobranie = json["delivery_method"]["prepaid"]

        koszyk = get_basket(user_id)

        suma = sum(float(x["article"]["Cena"]) for x in koszyk) + int(dostawa.CenaDostawy)

        platnosc = Platnosc()
        platnosc.ZamowienieIdZ = zam.IdZ
        platnosc.Wartosc = suma
        platnosc.Status = "NIEOPLACONE"

        artykuly = list(map(toOBM, koszyk))
        session.add(zam)
        session.add(dostawa)
        session.add(platnosc)
        session.add_all(artykuly)
        session.commit()
        return True

# coding: utf-8
from sqlalchemy import Column, Date, Float, ForeignKey, Integer, String, text, Boolean
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import relationship

Base = declarative_base()
metadata = Base.metadata


class Artykul(Base):
    __tablename__ = 'Artykul'

    IdT = Column(Integer, primary_key=True)
    Nazwa = Column(String(60), nullable=False)
    Cena = Column(Float(asdecimal=True))
    LiczbaSzt = Column(Integer)
    KodKreskowy = Column(String(20), nullable=False, unique=True, server_default=text("''"))
    CzyRecepta = Column(Boolean())


class RodzajUzytkownika(Base):
    __tablename__ = 'Rodzaj_Uzytkownika'

    IdRU = Column(Integer, primary_key=True)
    NazwaRU = Column(String(30), nullable=False, unique=True)


class StatusZamowienia(Base):
    __tablename__ = 'StatusZamowienia'

    IdSZ = Column(Integer, primary_key=True)
    NazwaSZ = Column(String(60), nullable=False, unique=True)


class Uzytkownik(Base):
    __tablename__ = 'Uzytkownik'

    IdK = Column(Integer, primary_key=True)
    IdRU = Column(ForeignKey('Rodzaj_Uzytkownika.IdRU'), nullable=False, index=True)
    Imie = Column(String(60), nullable=False)
    Nazwisko = Column(String(60), nullable=False)
    Adres = Column(String(100))
    Login = Column(String(30), nullable=False, unique=True)
    Email = Column(String(30), nullable=False, unique=True)
    Pesel = Column(String(20), unique=True)
    DataUrodzenia = Column(Date)
    Haslo = Column(String(100))

    Rodzaj_Uzytkownika = relationship('RodzajUzytkownika')


class Zamowienie(Base):
    __tablename__ = 'Zamowienie'

    IdZ = Column(Integer, primary_key=True)
    UzytkownicyIdK = Column(ForeignKey('Uzytkownik.IdK'), nullable=False, index=True)
    StatusZamowienia = Column(ForeignKey('StatusZamowienia.IdSZ'), nullable=False, index=True)
    DataZlozenia = Column(Date, nullable=False)
    DataNadania = Column(Date)
    DataDostarczenia = Column(Date)

    StatusZamowienia1 = relationship('StatusZamowienia')
    Uzytkownik = relationship('Uzytkownik')


class ArtykulWZamowieniu(Base):
    __tablename__ = 'Artykul_w_zamowieniu'

    TowaryIdT = Column(ForeignKey('Artykul.IdT'), primary_key=True, nullable=False)
    ZamowieniaIdZ = Column(ForeignKey('Zamowienie.IdZ'), primary_key=True, nullable=False, index=True)
    LiczbaSzt = Column(Integer, nullable=False)
    CenaWZamowieniu = Column(Float(asdecimal=True), nullable=False)

    Artykul = relationship('Artykul')
    Zamowienie = relationship('Zamowienie')


class Dostawa(Base):
    __tablename__ = 'Dostawa'

    IdD = Column(Integer, primary_key=True, unique=True)
    ZamowienieIdZ = Column(ForeignKey('Zamowienie.IdZ'), nullable=False, index=True)
    SposobDostawy = Column(String(100))
    CenaDostawy = Column(Integer)
    AdresDostawy = Column(String(100))
    PlatnoscPobranie = Column(Boolean(1))

    Zamowienie = relationship('Zamowienie')


class Platnosc(Base):
    __tablename__ = 'Platnosc'

    ZamowienieIdZ = Column(ForeignKey('Zamowienie.IdZ'), nullable=False, index=True)
    Wartosc = Column(Integer)
    Status = Column(String(100))
    Status = Column(String(100))
    IdP = Column(Integer, primary_key=True, unique=True)

    Zamowienie = relationship('Zamowienie')


class Reklamacja(Base):
    __tablename__ = 'Reklamacja'

    ZamowieniaIdZ = Column(ForeignKey('Zamowienie.IdZ'), nullable=False, unique=True)
    KwotaZwrotu = Column(Float(asdecimal=True), nullable=False)
    PowodZwrotu = Column(Integer, nullable=False)
    DodatkoweInformacje = Column(String(255))
    IdR = Column(Integer, primary_key=True, unique=True)

    Zamowienie = relationship('Zamowienie')

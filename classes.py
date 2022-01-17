# coding: utf-8
from sqlalchemy import Column, Date, Float, ForeignKey, Integer, String, Table
from sqlalchemy.dialects.mysql import BIT
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
    KodKreskowy = Column(String(20), nullable=False, unique=True)
    CzyRecepta = Column(BIT(1))

    # def __dict__(self):
    #     return {
    #         "IdT": self.IdT,
    #         "Nazwa": self.Nazwa,
    #         "Cena": self.Cena,
    #         "LiczbaSzt": self.LiczbaSzt
    #
    #     }


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


t_Dostawa = Table(
    'Dostawa', metadata,
    Column('ZamowienieIdZ', ForeignKey('Zamowienie.IdZ'), nullable=False, index=True),
    Column('SposobDostawy', String(4)),
    Column('CenaDostawy', Integer),
    Column('AdresDostawy', String(100)),
    Column('PlatnoscPobranie', BIT(1))
)


class Platnosc(Base):
    __tablename__ = 'Platnosc'

    ZamowienieIdZ = Column(ForeignKey('Zamowienie.IdZ'), nullable=False, index=True)
    IdPlatnosci = Column(Integer, primary_key=True)
    Wartosc = Column(Integer)
    Status = Column(String(10))

    Zamowienie = relationship('Zamowienie')


t_Reklamacja = Table(
    'Reklamacja', metadata,
    Column('ZamowieniaIdZ', ForeignKey('Zamowienie.IdZ'), nullable=False, unique=True),
    Column('KwotaZwrotu', Float(asdecimal=True), nullable=False),
    Column('PowodZwrotu', Integer, nullable=False),
    Column('DodatkoweInformacje', String(255))
)

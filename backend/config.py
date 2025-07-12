# config.py

import os
from dotenv import load_dotenv

load_dotenv()  # Cargar variables desde el archivo .env

class Config:
    SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://root@localhost/tiendaweb'
    SQLALCHEMY_TRACK_MODIFICATIONS = False

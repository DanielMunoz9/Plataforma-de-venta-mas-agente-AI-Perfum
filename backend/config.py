# config.py

import os
from dotenv import load_dotenv

load_dotenv()  # Cargar variables desde el archivo .env

class Config:
    SECRET_KEY = os.environ.get('SECRET_KEY') or 'clave-secreta-super-segura'
    SQLALCHEMY_DATABASE_URI = 'mysql+pymysql://root@localhost/tiendaweb'
    SQLALCHEMY_TRACK_MODIFICATIONS = False
    

#!/usr/bin/env python3
"""
Punto de entrada principal para la aplicación Flask
"""
import os
import sys

# Agregar el directorio backend al path para imports
sys.path.insert(0, os.path.join(os.path.dirname(__file__), 'backend'))

from backend.app import create_app

if __name__ == '__main__':
    app = create_app()
    app.run(
        host='127.0.0.1',
        port=5000,
        debug=True
    )
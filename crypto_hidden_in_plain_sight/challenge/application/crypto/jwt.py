import jwt, os

secret_key = os.urandom(16).hex()

def create_token(username):
    return jwt.encode({'username': username, 'egg': open('/flag.txt').read()}, secret_key, algorithm='HS256')
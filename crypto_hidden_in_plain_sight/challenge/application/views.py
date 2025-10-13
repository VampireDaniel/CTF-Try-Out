from app import app
from flask import Blueprint, render_template, request, make_response, redirect, url_for, flash
import models
from models.user import User
from crypto.jwt import create_token
from database import DB

bp = Blueprint('views', __name__)
database = DB()

@bp.route('/')
def home():
    return render_template('index.html')

@bp.route('/register', methods=['GET', 'POST'])
def register():
    if request.method == 'POST':
        data = request.form.to_dict()

        if not ('username' in data and 'password' in data):
            return render_template('register.html', error='You must provide your username and password.'), 400

        username = data['username']
        password = data['password']

        if not (10 <= len(username) <= 20):
            return render_template('register.html', error='The username must be 10 to 20 characters long.'), 400

        new_user = User(username, password)

        if database.fetch_user_by_username(new_user.username):
            return render_template('register.html', error='The user already exists in the database.'), 400

        if new_user != database.insert(new_user):
            return render_template('register.html', error='An internal database error occured. Contact an admin!'), 500

        token = create_token(username)
        flash('You have successfully registered!', category='message')
        response = make_response(redirect(url_for('views.home')))
        response.set_cookie('fascinating_cookie', token)
        return response

    return render_template('register.html'), 200
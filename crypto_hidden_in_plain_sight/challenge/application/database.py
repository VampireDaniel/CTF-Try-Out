from models.user import User

class DB:
    def __init__(self):
        self.db = {'users': []}
    
    def insert(self, entity):
        self.db['users'].append(entity)
        return entity
    
    def fetch_user_by_username(self, username):
        for user in self.db['users']:
            if user.username == username:
                return user

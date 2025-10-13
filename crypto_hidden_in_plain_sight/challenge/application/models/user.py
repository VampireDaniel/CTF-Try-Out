class User:
    def __init__(self, username, password):
        self.username = username
        self.password = password
    
    def __str__(self):
        return f'User({self.username}, {self.password})'
    
    def __json_serialize__(self):
        return {
            'username': self.username,
            'password': self.password,
        }
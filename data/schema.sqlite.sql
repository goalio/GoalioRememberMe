CREATE TABLE user_remember_me (
  sid VARCHAR(16) NOT NULL,
  token VARCHAR(16) NOT NULL,
  user_id INTEGER(11) NOT NULL,
  UNIQUE (sid,token,user_id),
  FOREIGN KEY(user_id) REFERENCES user(user_id) ON DELETE CASCADE ON UPDATE CASCADE
);
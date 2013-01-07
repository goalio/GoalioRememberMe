CREATE TABLE user_remember_me (
  sid VARCHAR(16) NOT NULL,
  token VARCHAR(16) NOT NULL,
  user_id INTEGER(11) NOT NULL,
  UNIQUE KEY sid (sid,token,user_id)
);

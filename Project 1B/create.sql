DROP TABLE IF EXISTS MovieGenre;
DROP TABLE IF EXISTS MovieDirector;
DROP TABLE IF EXISTS MovieActor;
DROP TABLE IF EXISTS Review;
DROP TABLE IF EXISTS MaxPersonID;
DROP TABLE IF EXISTS MaxMovieID;
DROP TABLE IF EXISTS Movie;
DROP TABLE IF EXISTS Actor;
DROP TABLE IF EXISTS Director;

CREATE TABLE Movie(id int NOT NULL PRIMARY KEY, title varchar(100), year int, rating varchar(10), company varchar(50), CHECK (year <= YEAR(GETDATE())))
ENGINE = INNODB;

CREATE TABLE Actor(id int NOT NULL PRIMARY KEY, last varchar(20), first varchar(20), sex varchar(60), dob date, dod date, CHECK (dob < GETDATE()))
ENGINE = INNODB;

CREATE TABLE Director(id int NOT NULL PRIMARY KEY, last varchar(20), first varchar(20), dob date, dod date, CHECK (dob < GETDATE()))
ENGINE = INNODB;

CREATE TABLE MovieGenre(mid int, genre varchar(20), FOREIGN KEY (mid) REFERENCES Movie (id))
ENGINE = INNODB;

CREATE TABLE MovieDirector(mid int REFERENCES Movie, did int, FOREIGN KEY (mid) REFERENCES Movie (id), FOREIGN KEY (did) REFERENCES Director (id))
ENGINE = INNODB;

CREATE TABLE MovieActor(mid int, aid int, role varchar(20), FOREIGN KEY (mid) REFERENCES Movie (id),FOREIGN KEY (aid) REFERENCES Actor(id))
ENGINE = INNODB;

CREATE TABLE Review(name varchar(20), time timestamp, mid int, rating int, comment varchar(500), FOREIGN KEY (mid) REFERENCES Movie (id))
ENGINE = INNODB;

CREATE TABLE MaxPersonID(id int NOT NULL)
ENGINE = INNODB;

CREATE TABLE MaxMovieID(id int NOT NULL)
ENGINE = INNODB;

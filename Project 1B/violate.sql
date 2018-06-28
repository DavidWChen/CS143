##Data Integrity Issue

##Movie must have id and title
##Actor must have id and first
##Director must have id and first
##MaxMovieID's and MaxPersonID'd is cannot be NULL
##etc.(see below)

##Three primary key constraints

##Movie must have unique id within table
##Actor must have unique id within table
##Director must have unique id within table


##Six referential integrity constraints

##mid in MovieActor must exist in Movie
##aid in MovieActor must exist in Actor
##mid in MovieDirector must exist in Movie
##did in MovieDirector must exist in Director
##mid in MovieGenre must exist in Movie
##mid in Review must exist in Movie

##Three CHECK constraints

##Actor's dob must be < dod & both must be before or at current date
##Director's dob must be < dod & both must be before or at current date
##Movie's date must be before or at current date


##PRIMARY KEY CONSTRAINTS
UPDATE Movie SET id = NULL WHERE id = 1;
UPDATE Actor SET id = NULL WHERE id = 1;
UPDATE Director SET id = NULL WHERE id = 1;
##these violate the Primary key contrainst as they cannot be null

INSERT INTO Actor VALUES (1, 'Actor', 'One', '', 20001212, 20001212);
INSERT INTO Actor VALUES (1, 'Actor', 'Uno', '', 20001212, 20001212); 

INSERT INTO Movie VALUES (1, 'Movie', 2000, 20001212, 20001212);
INSERT INTO Movie VALUES (1, 'Movie', 2000, 20001212, 20001212);

INSERT INTO Director VALUES (1, 'D', 'One',  20001212,  20001212);
INSERT INTO Director VALUES (1, 'D', 'Uno', 20001212,  20001212);

##Actors, Movies and Director must have unque primary keys
##these violate the Primary key contrainst


##REFERENCTIAL INTEGRITY CONSTRAINTS-

INSERT INTO MovieGenre VALUES (9000, 'Action');
##violates referential itegrity by trying to add directly to Movie Genre 
##without guarantee of the Movie actally existing
INSERT INTO MovieActor VALUES (123456, 12345678, "Human");
##violates referential itegrity by trying to add directly to MovieActor
##without guarantee of the Movie or Actor actally existing
UPDATE MovieActor SET mid=129184 WHERE aid = 1;
##violates referential itegrity by trying update MovieActor mid to a movie
##that may not exist
UPDATE MovieDirector SET mid=129184 WHERE did = 1;
##violates referential itegrity by trying update MovieDirectorr mid to a movie
##that may not exist
INSERT INTO MovieDirector VALUES (9001, 8999);
##violates referential itegrity by trying to add directly to MovieDirector
##without guarantee of the Movie or Actor actally existing

INSERT INTO Review VALUES ('Name', '2000-06-08 11:12:12', 123456789, 0, '');
##violates referential itegrity by trying to add directly to Review
##without guarantee of the Movie actally existing


##CHECK CONSTRAINTS
UPDATE Movie SET year = 5000 WHERE id = 1;
##violates constraint where year must be before or at current year

UPDATE Actor SET dob=20900101 WHERE id = 1;
UPDATE Director SET dob=20900101 WHERE id = 1;
##violates constraint where dob must be before or at current date



















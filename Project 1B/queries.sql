SELECT concat(first, ' ', last)
FROM Actor
WHERE id in (
	select aid from MovieActor where mid in (
		select id from Movie where title = 'Die Another Day'));
-- Gets the mid corresponding to Die Another Day
-- Uses it to find all the aid thatcorrespond to that mid in MovieActor
-- and then gets all the names og the actors corresponding to the aid
-- then formats the names


SELECT count(aid)
FROM MovieActor
WHERE aid in (
	SELECT aid
FROM MovieActor
GROUP BY aid
HAVING count(aid) > 1);

-- Gets all the aid from MoveActor that appear more than once
-- Then counts how many aid there out out of that group

SELECT title
FROM Movie
WHERE year > 2002 AND rating= 'PG-13';

-- gets the title of all the movies made after 2002 and are rated PG-13
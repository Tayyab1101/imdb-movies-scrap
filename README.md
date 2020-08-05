# Imdb Movies Scrapping

It is a simple script that can scrap movies from imdb using PHP.

### Target Link:

https://www.imdb.com/search/title/?year=2000,2000&title_type=feature&sort=moviemeter,asc&page=0&ref_=adv_nxt

### Features:

- You have the choice of selecting a specific time period (between years) i.e. 2004-2008
- You can specify any number of pages to scrap (At your own risk, because IMDB may ban your IP if you scrapped a lot of pages at one time)
- Here is a single movie demo data that you will get
  `[0] => Array ( 
  [name] => The Dark Knight 
  [year] => 2008 
  [image] => https://m.media-amazon.com/images/M/MV5BMTMxNTMwODM0NF5BMl5BanBnXkFtZTcwODAyMTk2Mw@@._V1_UX67_CR0,0,67,98_AL_.jpg 
  [certificate] => PG-13 
  [runtime] => 152 
  [genre] => Action, Crime, Drama 
  [imdb_rating] => 9 
  [metascore] => 84 
  [description] => When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice. 
  [directors] => Christopher Nolan 
  [stars] => Christian Bale, Heath Ledger, Aaron Eckhart, Michael Caine 
  [votes] => 2232742 
  [gross] => 534858444 
  )`

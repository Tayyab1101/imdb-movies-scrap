<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IMDB SCRAP</title>
</head>
<style>
*{
    font-size: 1.1em;
}
</style>
<body>
   <form method="GET" action="/imdb.php">
    <label
     for="">From: 
        <input type="number" name="year_start" placeholder="e.g 2018">
    </label>
    <label
     for="">To: 
        <input type="number" name="year_end" placeholder="e.g 2020">
    </label>
    <label
     for="">Pages: 
        <input type="number" name="pages" placeholder="e.g 3">
    </label>
        <button type="submit">SCRAP</button>
   </form> 
</body>
</html>

<?php 
function movies_data($name,$pattern,$index,$source,&$destination,$flag){
    preg_match_all($pattern,$source,$match);
    if($flag == true){
    $destination[$name] = $match[$index];
    }else{
    return $match;
    }
}

function imdb_data($year_start,$year_end,$page){
    $all_data = array();
    if($page == 0){
        $start = 1;
    }else{
    $start = ($page * 50) - 49;
    }

$curl = curl_init();

$url = "https://www.imdb.com/search/title/?year=$year_start,$year_end&title_type=feature&sort=moviemeter,asc&page=0&start=$start&ref_=adv_nxt";

curl_setopt($curl,CURLOPT_URL,$url);
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

$result = curl_exec($curl);

$movies = array();


//$name = name of movie
//$index = capture group number of regex pattern
//$source = the source code
//$destination = array where to store the output
//$flag - True for saving output and return nothing , False for just return matches


//match movie title
movies_data('name','/<a href="\/title\/tt[0-9]+\/\?ref.*[^>]+>(.*)<\/a>/',1,$result,$movies,true);

//match movie title
movies_data('year','/<span class="lister-item-year text-muted unbold">\(([0-9]+)\)<\/span>/',1,$result,$movies,true);

//match movie img url
movies_data('image','/loadlate="(.*)"/',1,$result,$movies,true);

//match certificate, runtime, genre
$crnBlock = movies_data('crn','/<p class="text-muted ">.*?<\/p>/s',0,$result,$movies,false);
$crnBlock = $crnBlock[0];
// for certificate
for($i=0;$i<count($crnBlock);$i++){
    if(!strpos($crnBlock[$i],"certificate")){
        $movies['certificate'][$i] = '';
    }else{
        $tempArray = movies_data('cert','/class="certificate">(.*?)<\/span>/',1,$crnBlock[$i],$movies,false);
        $movies['certificate'][$i] = $tempArray[1][0];
    }
}
// for runtime
for($i=0;$i<count($crnBlock);$i++){
    if(!strpos($crnBlock[$i],"runtime")){
        $movies['runtime'][$i] = '';
    }else{
        $tempArray = movies_data('runtime','/class="runtime">[ \n]*([0-9]*?)\smin[ \n]*<\/span>/',1,$crnBlock[$i],$movies,false);
        $movies['runtime'][$i] = $tempArray[1][0];
    }
}
// for genre
for($i=0;$i<count($crnBlock);$i++){
    if(!strpos($crnBlock[$i],"genre")){
        $movies['genre'][$i] = '';
    }else{
        $tempArray = movies_data('genre','/class="genre">[ \n]*(.*?)[ \n]*<\/span>/s',1,$crnBlock[$i],$movies,false);
        $movies['genre'][$i] = $tempArray[1][0];
    }
}

//match movie rating
$rtBlock = movies_data('rtmt','/<div class="ratings-bar">.*?<\/div>/s',0,$result,$movies,false);
$rtBlock = $rtBlock[0];
// for imdb rating
for($i=0;$i<count($rtBlock);$i++){
    if(!preg_match('/inline-block\sratings-imdb-rating"\sname="ir"\sdata-value="(.*?)">/s',$rtBlock[$i])){
        $movies['imdb_rating'][$i] = '';
    }else{
        $tempArray = movies_data('rating','/inline-block\sratings-imdb-rating"\sname="ir"\sdata-value="(.*?)">/s',1,$rtBlock[$i],$movies,false);
        $movies['imdb_rating'][$i] = $tempArray[1][0];
    }
}
//for metascore and description
$mtdBlock = movies_data('rtmt','/Delete" rel="nofollow.*?<\/div>[ \n]*<\/div>[ \n]*<\/div>(.*?)<\/div>.<p class="text-muted">\s*(.*?)<\/p>/sX',0,$result,$movies,false);
$mtBlock = $mtdBlock[1];
//for metascore
for($i=0;$i<count($mtBlock);$i++){
    if(!preg_match('/ratings-metascore">(.*?)<\/div>/s',$mtBlock[$i])){
        $movies['metascore'][$i] = '';
    }else{
        $tempArray = movies_data('metascr','/ratings-metascore">.*?">.*?([0-9]*).*<\/div>/s',1,$mtBlock[$i],$movies,false);
        $movies['metascore'][$i] = $tempArray[1][0];
    }
}
//for description
for($i=0;$i<count($mtdBlock[2]);$i++){
    if(strpos($mtdBlock[2][$i],"Add a Plot") !== false){
        $movies['description'][$i] = '';
    }else{
        if(strpos($mtdBlock[2][$i],"...") !== false){
            $mtdBlock[2][$i] = substr($mtdBlock[2][$i], 0, strpos($mtdBlock[2][$i], " ..."));
        }
        $movies['description'][$i] = $mtdBlock[2][$i];
    }
}

//for directors,starts and votes
$dsvBlock = movies_data('rtmt','/<p class="">(.*?)<\/p>(.*?)<\/div>/s',0,$result,$movies,false);
$dsBlock = $dsvBlock[1];

//for directors
for($i=0;$i<count($dsBlock);$i++){
    if(strpos($dsBlock[$i],"Director") == false && strpos($dsBlock[$i],"Directors") == false){
            $movies['directors'][$i] = '';
    }else{
        $tempArray = movies_data('directors','/<p class="">.*?Director:.*?<a.*?>(.*?)<\/a>.*?<\/p>/s',1,$dsvBlock[0][$i],$movies,false);
        // if there are more than one directors
        if(empty($tempArray[0]) && empty($tempArray[1])){
            if(strpos($dsBlock[$i],"Stars") == false){
                $tempArray = movies_data('directors','/[ \n]>(.*?)<\/a/s',1,$dsvBlock[0][$i],$movies,false);
                $tempArray[1][0] = implode(", ",$tempArray[1]);
            }else{
                $directors = movies_data('directors','/Directors:(.*?)<span/s',1,$dsvBlock[0][$i],$movies,false);
                $directors = implode(',',$directors[1]);
                $tempArray = movies_data('directors','/[ \n]>(.*?)<\/a/s',1,$directors,$movies,false);
                $tempArray[1][0] = implode(", ",$tempArray[1]);
            }
        }
        $movies['directors'][$i] = $tempArray[1][0];
    }
}

//for stars
for($i=0;$i<count($dsBlock);$i++){
    if(strpos($dsBlock[$i],"Stars") == false){
            $movies['stars'][$i] = '';
    }else{
        $stars = movies_data('stars','/Stars.*/s',1,$dsvBlock[1][$i],$movies,false);
        $stars = $stars[0][0];
        $tempArray = movies_data('stars','/[ \n]>(.*?)<\/a/s',1,$stars,$movies,false);
        $stars = implode(", ",$tempArray[1]);
        $movies['stars'][$i] = $stars;
    }
}

//for votes
for($i=0;$i<count($dsvBlock[2]);$i++){
    if(strpos($dsvBlock[2][$i],"Votes") == false){
            $movies['votes'][$i] = '';
    }else{
        $votes = movies_data('votes','/data-value="(.*?)"/s',1,$dsvBlock[2][$i],$movies,false);
        $movies['votes'][$i] = $votes[1][0];
    }
};

//for gross
for($i=0;$i<count($dsvBlock[2]);$i++){
    if(strpos($dsvBlock[2][$i],"Gross") == false){
            $movies['gross'][$i] = '';
    }else{
        $gross = movies_data('gross','/Gross:.*?data-value="(.*?)"/s',1,$dsvBlock[2][$i],$movies,false);
        $b = str_replace( ',', '', $gross[1][0] );
        if( is_numeric( $b ) ) {
            $gross[1][0] = $b;
        }
        $movies['gross'][$i] = $gross[1][0];
    }
};

    //save all data in a nicely formatted array
    foreach($movies as $key => $value) {
        for ($i = 0; $i < count ($movies[$key]);$i++){
            $data[$i][$key] = $movies[$key][$i];
        }
    }
    
    $all_data = array_merge($data,$all_data); 
    return $all_data;
}

//how many pages you wanna scrap

if(isset($_GET['year_start']) == true && isset($_GET['year_end']) == true && isset($_GET['pages']) == true){
    $year_start = $_GET['year_start'];
    $year_end = $_GET['year_end'];
    $pages = $_GET['pages'];
}

$data = array();
for($i=1;$i<=$pages;$i++){
$data = array_merge($data,imdb_data($year_start,$year_end,$i));
}
echo "<pre>";
print_r($data);
echo "</pre>";

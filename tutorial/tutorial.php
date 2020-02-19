<?php

//カセット
interface CassettoInterface{
    public function getTitle();
}

//ファミコン
interface FamicomInterface{
    public function play();
}

//Twitter
interface TweetInterface{
    public function post());
}


class Casset implements CassetInterface{
    public function __construct($title = null){
        $this->title = $title;
    }
    public function getTitle(){
        return $this->title;
    }
}

class Famicom implements FamicomInterface{
    private $cassetto;
    public function __construct($cassetto){
        $this->casset = $cassetto
    }
    public function play(){
        echo '"'.$this->cassetto->getTitle().'"をプレイします<br>';
    }
}

class Tweet implements TweetInterface{
    public function post(){
        echo '「賢章さんしんどい」とつぶやきました<br>';
    }
}


class Niconama{
    private $cassetto;
    private $famicom;
    private $tweet;
    
    public function __construct(CassettoInterface $cassetto, FamicomInterface $famicom, TweetInterface $tweet){
        $this->cassetto = $cassetto;
        $this->famicom = $famicom;
        $this->tweet = $tweet;
    }

    public function play(){
        echo '"'.$this->cassetto->getTitle().'"をニコ生で実況します<br>';
        $this->famicom->play();
    }

    public function tweet(){
        $this->tweet->post();
    }
}


$cassetto = new Cassetto('UNO');
$famicom = new Famicom($cassetto);
$tweet = new Tweet();
$niconama = new Niconama($cassetto, $famicom, $tweet);
$niconama->play();
$niconama->tweet();
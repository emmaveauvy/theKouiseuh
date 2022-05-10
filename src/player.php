<?php

require_once('init.php');

function getPlayer($id) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM players WHERE id = ?");

    $sth->execute(array($id));

    return $sth->fetch(PDO::FETCH_ASSOC);
}

function addPlayer($name, $id_quiz) {
    $PDO = getPDO();
    $sth = $PDO->prepare("INSERT INTO players(name, id_quiz, score) values( ?, ?, ?)");
	$sth->execute(array($name, $id_quiz, 0));

    return intval($PDO->lastInsertId());//return l'id du player
}

function listPlayer() {
    $PDO = getPDO();
    $sth = $PDO->query("SELECT name, score FROM players");

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

function gePlayer($id) {
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT name, score FROM players WHERE (id = ?)");
    $sth->execute(array($id));

    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

function deletePlayer($id){
    $PDO = getPDO();
    $sth = $PDO->prepare("DELETE FROM players WHERE id = ?");
    $sth->execute(array($id));
}

function updateScore($id, $id_question){
    

    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT time FROM questions WHERE (id = ?)");
    $sth->execute(array($id_question));

    $data = $sth->fetchAll(PDO::FETCH_ASSOC);

    //change timezone
    date_default_timezone_set('Europe/Paris');
    //str to int
    $questiontime = strtotime($data[0]['time']);

    $delta = time() - $questiontime;

    $score = 30 - $delta; //points max = 30, min = 15 avec réponse juste

    $sth = $PDO->prepare("SELECT score FROM players WHERE (id = ?)");
    $sth->execute(array($id));
    $data = $sth->fetchAll(PDO::FETCH_ASSOC);
    $currentScore = $data[0]["score"];

    $sth = $PDO->prepare("UPDATE players SET score = ? WHERE id = ?");
    $sth->execute(array($currentScore + $score, $id));

    return($sth->fetchAll(PDO::FETCH_ASSOC));
}

function verifName($name){
    $PDO = getPDO();
    $sth = $PDO->prepare("SELECT * FROM players WHERE name = ?");
    $sth->execute(array($name));

    if($sth->fetchAll(PDO::FETCH_ASSOC)){
        return true;
    }
    return false;
}

?>
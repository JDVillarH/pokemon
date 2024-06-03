<?php

set_time_limit(1200);

require_once 'conexion.php';
require_once 'functions.php';

$dbConnection = new Database();

// Almacenar los datos de la API en la base de datos
$statsCurl = (array) cURL("https://pokeapi.co/api/v2/stat")["results"];
$typesCurl = (array) cURL("https://pokeapi.co/api/v2/type")["results"];
$movesCurl = (array) cURL("https://pokeapi.co/api/v2/move?limit=100000&offset=0")["results"];
$abilitiesCurl = (array) cURL("https://pokeapi.co/api/v2/ability?limit=100000&offset=0")["results"];
$pokemonCurl = (array) cURL("https://pokeapi.co/api/v2/pokemon?limit=100000&offset=0")["results"];

$dbConnection->insert("stats", "name", "('" . buildNameValues($statsCurl) . "')");
$dbConnection->insert("types", "name", "('" . buildNameValues($typesCurl) . "')");
$dbConnection->insert("moves", "name", "('" . buildNameValues($movesCurl) . "')");
$dbConnection->insert("abilities", "name", "('" . buildNameValues($abilitiesCurl) . "')");

// Almacenar los datos de los pokemones en arrays para evitar consultas innecesarias dentro del bucle de relaciones
$statsArray = [];
$typesArray = [];
$movesArray = [];
$abilitiesArray = [];

$statsDB = $dbConnection->select("stats", "name, id", "1")->getRows();
$typesDB = $dbConnection->select("types", "name, id", "1")->getRows();
$movesDB = $dbConnection->select("moves", "name, id", "1")->getRows();
$abilitiesDB = $dbConnection->select("abilities", "name, id", "1")->getRows();

foreach ($statsDB as $statsResult) {
    $statsArray[$statsResult->name] = $statsResult->id;
}

foreach ($typesDB as $typesResult) {
    $typesArray[$typesResult->name] = $typesResult->id;
}

foreach ($movesDB as $movesResult) {
    $movesArray[$movesResult->name] = $movesResult->id;
}

foreach ($abilitiesDB as $abilitiesResult) {
    $abilitiesArray[$abilitiesResult->name] = $abilitiesResult->id;
}

// Almacenar los datos relacionales de los pokemones en la base de datos
foreach ($pokemonCurl as $pokemon) {
    $pokemonInfo = (array) cURL($pokemon["url"]);
    $pokemonName = $pokemonInfo["name"];
    $pokemonImage = $pokemonInfo["sprites"]["other"]["official-artwork"]["front_default"];

    // Pokemon
    $pokemonId = $dbConnection->insert("pokemon", "name, image", "('$pokemonName', '$pokemonImage')");
    if (!is_int($pokemonId)) {
        continue;
    }

    // Estadisticas, tipos, movimientos y habilidades
    if (!empty($pokemonInfo["stats"])) {
        $dbConnection->customMultiQuery(insertRelations("stats", "stat", $pokemonInfo["stats"], $statsArray));
    }

    if (!empty($pokemonInfo["types"])) {
        $dbConnection->customMultiQuery(insertRelations("types", "type", $pokemonInfo["types"], $typesArray));
    }

    if (!empty($pokemonInfo["moves"])) {
        $dbConnection->customMultiQuery(insertRelations("moves", "move", $pokemonInfo["moves"], $movesArray));
    }

    if (!empty($pokemonInfo["abilities"])) {
        $dbConnection->customMultiQuery(insertRelations("abilities", "ability", $pokemonInfo["abilities"], $abilitiesArray));
    }
}

echo "Datos extraidos y almacenados correctamente.";

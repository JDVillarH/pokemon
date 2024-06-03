<?php

require_once 'functions.php';
header("Access-Control-Allow-Origin: *");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    require_once 'conexion.php';

    $paramId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;
    $paramType = filter_input(INPUT_GET, "type", FILTER_VALIDATE_INT) ?? 0;
    $paramStat = filter_input(INPUT_GET, "stat", FILTER_VALIDATE_INT) ?? 0;
    $paramPage = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT) ?? 1;

    $dbConnection = new Database();

    //Si se solicitan los tipos de Pokémon
    if (isset($_GET["types"]) && count($_GET) === 1) {
        $types = $dbConnection->select("types", "id, name", "TRUE")->getRows();
        die(jsonResponse(200, $types));
    }

    // Si se solicita un Pokémon específico
    if ($paramId >= 1) {

        $pokemonResult = $dbConnection->select("pokemon", "*", "id = $paramId")->getRow();
        $pokemonResult->stats = $dbConnection->select("pokemon_stat_view", "name, base_stat", "pokemon_id = $paramId")->getRows();
        $pokemonResult->types = $dbConnection->select("pokemon_type_view", "id, name", "pokemon_id = $paramId")->getRows();
        $pokemonResult->moves = $dbConnection->select("pokemon_move_view", "name", "pokemon_id = $paramId")->getRows();
        $pokemonResult->abilities = $dbConnection->select("pokemon_ability_view", "name", "pokemon_id = $paramId")->getRows();

        die(jsonResponse(200, $pokemonResult));
    }

    if ($paramId === 0) {

        // Filtro por tipo
        $whereString = ($paramType >= 1) ? "p.id IN (SELECT tp.pokemon_id FROM types_pokemon AS tp WHERE tp.type_id = $paramType)" : "TRUE";

        // Paginación
        $perPage = 25;
        $totalPokemon = $dbConnection->select("pokemon p", "COUNT(id) AS total_pages", $whereString)->getRow()->total_pages;
        $totalPages = ceil($totalPokemon / $perPage);
        $offset = max(0, ($paramPage - 1) * $perPage);

        // Obtener los Pokémon
        $pokemonResult = [];
        if ($paramStat >= 1) { // Si se solicita ordenar por estadísticas

            $whereString .= " AND sp.stat_id = $paramStat";
            $orderBy = "ORDER BY sp.base_stat " . (isset($_GET["order"]) && strtoupper($_GET["order"]) === "ASC" ? "ASC" : "DESC");

            $pokemonResult = $dbConnection->customSingleQuery("
                SELECT p.*
                FROM pokemon p
                INNER JOIN stats_pokemon AS sp ON sp.pokemon_id = p.id
                WHERE $whereString $orderBy
                LIMIT $perPage OFFSET $offset
            ")->getRows();
        } else {
            $pokemonResult = $dbConnection->select("pokemon p", "*", "$whereString LIMIT $perPage OFFSET $offset")->getRows();
        }

        foreach ($pokemonResult as $key => $pokemon) {
            $pokemonResult[$key]->stats = $dbConnection->select("pokemon_stat_view", "id, name, base_stat", "pokemon_id = $pokemon->id")->getRows();
            $pokemonResult[$key]->types = $dbConnection->select("pokemon_type_view", "id, name", "pokemon_id = $pokemon->id")->getRows();
            $pokemonResult[$key]->moves = $dbConnection->select("pokemon_move_view", "name", "pokemon_id = $pokemon->id")->getRows();
            $pokemonResult[$key]->abilities = $dbConnection->select("pokemon_ability_view", "name", "pokemon_id = $pokemon->id")->getRows();
        }

        die(jsonResponse(200, $pokemonResult, getPagination($perPage, $totalPokemon, $paramPage)));
    } else {
        die(jsonResponse(400));
    }
} else {
    die(jsonResponse(405));
}

<?php

/**
 * Envía una solicitud cURL a la URL especificada y devuelve la respuesta.
 *
 * @param string $url La URL a la que se enviará la solicitud cURL.
 * @return array La respuesta de la solicitud cURL.
 */

function cURL($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

/**
 * Construye una cadena de valores a partir de un array.
 *
 * @param array $array El array del que se extraerán los valores.
 * @return string El string de valores.
 */
function buildNameValues($array)
{
    return implode("'), ('", array_column($array, "name"));
}

/**
 * Inserta relaciones en la base de datos.
 * 
 * @param string $table El nombre de la tabla en la que se insertarán las relaciones.
 * @param string $searchValue El valor que se buscará en el array de datos.
 * @param array $data El array de datos que se utilizará para insertar las relaciones.
 * @param array $searchName El array de búsqueda que se utilizará para encontrar el ID de la relación.
 */
function insertRelations($table, $searchValue, $data, $searchName)
{
    global $pokemonId;

    $values = [];
    $relationsQuery = "";

    $insertColumns = "(pokemon_id, {$searchValue}_id" . (($table === "stats") ? ", base_stat)" : ")");
    $relationsQuery .= "INSERT INTO {$table}_pokemon $insertColumns VALUES ";

    foreach ($data as $item) {
        $itemId = $searchName[$item[$searchValue]["name"]];
        if ($itemId === null) continue;

        $baseStat = ($table === "stats") ? $item["base_stat"] : null;
        $values[] = "($pokemonId, $itemId" . (($table === "stats") ? ", $baseStat" : "") . ")";
    }

    $relationsQuery .= implode(', ', $values) . ";";
    return $relationsQuery;
}


/**
 * Devuelve una respuesta JSON con el estado y los resultados especificados.
 *
 * @param int $status El estado de la respuesta.
 * @param array $results Los resultados de la respuesta.
 * @param string $next La URL de la siguiente página.
 * @param string $previous La URL de la página anterior.
 * @return string La respuesta JSON.
 */
function jsonResponse($status, $results = [], $pagination = [])
{

    if (empty($pagination)) {
        $response = ["status" => $status, "results" => $results];
    } else {
        $response = ["status" => $status, "pagination" => $pagination, "results" => $results];
    }

    header("Content-Type: application/json");
    echo json_encode($response);
    http_response_code($status);
}

/**
 * Obtiene la URL actual.
 *
 * @return string La URL actual.
 */
function getCurrentURL()
{
    return (isset($_SERVER['HTTPS']) ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}


/**
 * Obtiene la paginación de la URL actual.
 *
 * @param int $perPage La cantidad de resultados por página.
 * @param int $totalRows La cantidad total de resultados.
 * @param int $page La página actual.
 * @return array La paginación.
 */
function getPagination($perPage, $totalRows, $page)
{
    $totalPages = ceil($totalRows / $perPage);
    $currentPage = max(1, $page ?? 1);
    $currentURL = getCurrentURL();

    if (strpos($currentURL, "page=$currentPage") === false) {
        $currentURL .= (strpos($currentURL, "?") === false) ? "?page=$currentPage" : "&page=$currentPage";
    }

    $nextPage = ($currentPage < $totalPages) ? str_replace("page=$currentPage", "page=" . ($currentPage + 1), $currentURL) : null;
    $previousPage = ($currentPage > 1) ? str_replace("page=$currentPage", "page=" . ($currentPage - 1), $currentURL) : null;

    return ["next" => $nextPage, "previous" => $previousPage];
}

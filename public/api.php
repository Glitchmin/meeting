<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require __DIR__ . '/../vendor/autoload.php';
class MyDB extends SQLite3 {
    function __construct() {
       $this->open('../participants.db');
    }
 }
$db= new MyDB();
$app = new \Slim\App;
$app->get(
    '/api/participants',
    function (Request $request, Response $response, array $args) use ($db) {
        
      $participants = [];
      $sql = "SELECT id, firstname, lastname FROM participant";
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ) {
        $participants[] = $row; 
    }
    $db->close();
        return $response->withJson($participants);
    }
);

$app->post(
    '/api/participants',
    function (Request $request, Response $response, array $args) use ($db) {
		$requestData = $request->getParsedBody();
		if (!isset($requestData['firstname']) || !isset($requestData['lastname'])){
			return $response->withStatus(418);
		}
        $sql = "INSERT INTO participant (firstname, lastname) VALUES('$requestData[firstname]', '$requestData[lastname]');";
        $db->query($sql);
        return $response->withStatus(201);
    }
);

$app->delete(
    '/api/participants/{id}',
    function (Request $request, Response $response, array $args) use ($db) {
        $sql = "DELETE FROM participant WHERE id = :flower";
        $stmt = $db->prepare($sql);
        $stmt->bindValue('flower', $args['id']);
        $ret = $stmt->execute();
        return $response->withStatus(204);
    }
);



$app->run();

<?php
require_once __DIR__ . "/config.php";

$db = getDB();

// Filtros
$where = [];
$params = [];

if (!empty($_GET["player"])) {
    $where[] = "player LIKE :player";
    $params[":player"] = "%" . $_GET["player"] . "%";
}

if (!empty($_GET["rarity"])) {
    $where[] = "p.rarity = :rarity";
    $params[":rarity"] = $_GET["rarity"];
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

$query = "
SELECT l.*, p.name AS petName, p.fullName, p.rarity, p.price
FROM logs l
LEFT JOIN pets p ON p.log_id = l.id
$whereSql
ORDER BY l.timestamp DESC
LIMIT 200
";

$stmt = $db->prepare($query);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Dashboard - Pet Scanner</title>
<style>
body { font-family: Arial, sans-serif; background: #111; color: #eee; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th, td { border: 1px solid #333; padding: 6px; text-align: left; }
th { background: #222; }
input, select { background: #222; color: #fff; border: 1px solid #333; padding: 4px; }
button { background: #444; color: #fff; border: none; padding: 5px 10px; cursor: pointer; }
button:hover { background: #666; }
</style>
</head>
<body>
<h2>🐾 Pet827 Dashboard</h2>

<form method="get">
    Jogador: <input type="text" name="player" value="<?= htmlspecialchars($_GET["player"] ?? "") ?>">
    Raridade:
    <select name="rarity">
        <option value="">Todas</option>
        <option value="OG">OG</option>
        <option value="Secret">Secret</option>
        <option value="Brainrot God">Brainrot God</option>
    </select>
    <button type="submit">Filtrar</button>
</form>

<table>
<tr>
    <th>Data/Hora</th>
    <th>Jogador</th>
    <th>Pet</th>
    <th>Raridade</th>
    <th>Preço</th>
    <th>PlaceId</th>
</tr>
<?php foreach ($rows as $r): ?>
<tr>
    <td><?= htmlspecialchars($r["timestamp"]) ?></td>
    <td><?= htmlspecialchars($r["player"]) ?></td>
    <td><?= htmlspecialchars($r["petName"]) ?></td>
    <td><?= htmlspecialchars($r["rarity"]) ?></td>
    <td><?= htmlspecialchars($r["price"]) ?></td>
    <td><?= htmlspecialchars($r["placeId"]) ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>

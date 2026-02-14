<?php

$pdo = new PDO('mysql:host=localhost;dbname=photographar_db', 'root', '');

echo "=== CATEGORIES ===\n";
$stmt = $pdo->query('SELECT id, name, slug FROM categories LIMIT 20');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  echo $row['slug'] . " => " . $row['name'] . "\n";
}

echo "\n=== CITIES ===\n";
$stmt = $pdo->query('SELECT id, name, slug FROM cities LIMIT 20');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  echo $row['slug'] . " => " . $row['name'] . "\n";
}

echo "\n=== PHOTOGRAPHER_CATEGORY PIVOT ===\n";
$stmt = $pdo->query('SELECT photographer_id, category_id FROM photographer_category LIMIT 20');
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  echo "P:" . $row['photographer_id'] . " -> C:" . $row['category_id'] . "\n";
}

echo "\n=== TEST QUERY: Wedding Photographers ===\n";
$stmt = $pdo->prepare('
  SELECT DISTINCT p.id, u.name, cat.name as category
  FROM photographers p
  JOIN users u ON p.user_id = u.id
  JOIN photographer_category pc ON p.id = pc.photographer_id
  JOIN categories cat ON pc.category_id = cat.id
  WHERE cat.slug = ?
  AND p.is_verified = 1
  LIMIT 5
');
$stmt->execute(['wedding-photography']);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Query returned " . count($results) . " rows\n";
foreach ($results as $row) {
  echo "P:" . $row['id'] . " - " . $row['name'] . " - " . $row['category'] . "\n";
}

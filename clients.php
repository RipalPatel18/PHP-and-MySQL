<?php
require_once 'connect.php';

$sql = "SELECT client_id, name, email, phone, created_at FROM clients ORDER BY name";
$res = $mysqli->query($sql);

if (!$res) { echo "Query error: ".$mysqli->error; exit(); }
?>

<!doctype html>
<html lang="en">
<head><meta charset="utf-8"><title>Clients</title></head>
<body>
<header><h1>Clients</h1><a class="back" href="index.php">← Back</a></header>
<main class="container">
  <div class="grid">

    <?php while ($row = $res->fetch_assoc()): 
      $is_new = (strtotime($row['created_at']) >= strtotime('-30 days'));
    ?>

      <article class="card <?php echo $is_new ? 'highlight' : ''; ?>">
        <h2><?php echo htmlspecialchars($row['name']); ?></h2>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
        <p><strong>Phone:</strong> <?php echo htmlspecialchars($row['phone']); ?></p>
        <p class="muted">Joined: <?php echo date('M j, Y', strtotime($row['created_at'])); ?></p>

        <?php if ($is_new): ?>
          <span class="badge new">New client</span>

        <?php else: ?>
          <span class="badge">Established</span>

        <?php endif; ?>
      </article>

    <?php endwhile; $res->free(); $mysqli->close(); ?>
  </div>
</main>
</body>
</html>

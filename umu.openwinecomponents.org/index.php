<?php

include 'dbinfo.php';

// Connect to the database
try {
  $pdo = new PDO(
    "mysql:host=".DB_HOST.";dbname=".DB_NAME,
    DB_USER, DB_PASSWORD, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]
  );
} catch (PDOException $e) {
  die("ERROR: Could not connect. " . $e->getMessage());
}

// Check if form has been submitted
if (isset($_POST["search"])) {
  $stmt = $pdo->prepare("
  SELECT
  g.title,
  g.acronym,
  gr.umu_id,
  gr.store,
  gr.codename,
  gr.exe_string,
  gr.notes
  FROM game g
  INNER JOIN gamerelease gr ON g.id = gr.umu_id
  WHERE
  g.title LIKE :search
  OR g.acronym LIKE :search
  OR gr.codename LIKE :search
  OR gr.exe_string LIKE :search
  ");
  $stmt->execute([":search" => "%".$_POST["search"]."%"]);
  $results = $stmt->fetchAll();
}
?>

<!-- HTML form -->
<!DOCTYPE html>
<html>
<head>
<title>UMU Database Search</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root {
  --bg: #0d1118;
  --panel: rgba(17, 24, 35, 0.92);
  --panel-strong: #131c28;
  --border: rgba(142, 169, 196, 0.16);
  --border-strong: rgba(142, 169, 196, 0.34);
  --text: #edf3fb;
  --muted: #9baec3;
  --accent: #f28f52;
  --accent-dark: #ffb480;
  --accent-soft: rgba(242, 143, 82, 0.16);
  --shadow: 0 24px 70px rgba(0, 0, 0, 0.38);
}

* {
  box-sizing: border-box;
}

html {
  font-family: "IBM Plex Sans", "Segoe UI", Helvetica, Arial, sans-serif;
  color: var(--text);
  background:
    radial-gradient(circle at top left, rgba(242, 143, 82, 0.18), transparent 28%),
    radial-gradient(circle at top right, rgba(85, 137, 214, 0.16), transparent 24%),
    linear-gradient(180deg, #0f1621 0%, #0c1119 56%, #090d14 100%);
}

body {
  margin: 0;
  min-height: 100vh;
}

a {
  color: var(--accent-dark);
}

a:hover {
  color: var(--accent);
}

.page {
  width: min(1180px, calc(100% - 2rem));
  margin: 0 auto;
  padding: 2rem 0 3rem;
}

.hero {
  background: linear-gradient(145deg, rgba(22, 31, 44, 0.95), rgba(14, 21, 32, 0.94));
  border: 1px solid var(--border);
  border-radius: 28px;
  box-shadow: var(--shadow);
  overflow: hidden;
}

.hero-inner {
  padding: 2rem;
}

.eyebrow {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.4rem 0.8rem;
  border-radius: 999px;
  background: var(--accent-soft);
  color: var(--accent-dark);
  font-size: 0.82rem;
  font-weight: 700;
  letter-spacing: 0.08em;
  text-transform: uppercase;
}

h1 {
  margin: 1rem 0 0.75rem;
  font-family: "Space Grotesk", "IBM Plex Sans", sans-serif;
  font-size: clamp(2.2rem, 5vw, 4.2rem);
  line-height: 0.96;
  letter-spacing: -0.04em;
}

.lead {
  max-width: 46rem;
  margin: 0 0 1.6rem;
  color: var(--muted);
  font-size: 1.05rem;
  line-height: 1.65;
}

.search-form {
  display: grid;
  grid-template-columns: minmax(0, 1fr) auto;
  gap: 0.9rem;
  margin-bottom: 1.25rem;
}

.search-input,
.search-button {
  border: 1px solid transparent;
  border-radius: 16px;
  font: inherit;
}

.search-input {
  min-width: 0;
  padding: 1rem 1.1rem;
  background: var(--panel-strong);
  border-color: var(--border);
  color: var(--text);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.03);
}

.search-input:focus {
  outline: none;
  border-color: var(--accent);
  box-shadow: 0 0 0 4px rgba(162, 76, 44, 0.12);
}

.search-button {
  padding: 1rem 1.25rem;
  background: linear-gradient(180deg, #ef9258 0%, #cf6a30 100%);
  color: #140d08;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 12px 24px rgba(207, 106, 48, 0.24);
}

.search-button:hover {
  background: linear-gradient(180deg, #f79d66 0%, #d76528 100%);
}

.meta {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 0.85rem;
  margin-top: 1.5rem;
}

.meta-card {
  padding: 1rem 1.1rem;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid var(--border);
  border-radius: 18px;
}

.meta-card strong {
  display: block;
  margin-bottom: 0.35rem;
  font-size: 0.86rem;
  letter-spacing: 0.03em;
  text-transform: uppercase;
  color: var(--muted);
}

.meta-card span,
.meta-card a {
  line-height: 1.5;
}

.results-panel {
  margin-top: 1.5rem;
  background: var(--panel);
  border: 1px solid var(--border);
  border-radius: 24px;
  box-shadow: var(--shadow);
  overflow: hidden;
}

.results-header {
  display: flex;
  justify-content: space-between;
  align-items: end;
  gap: 1rem;
  padding: 1.25rem 1.4rem;
  background: linear-gradient(180deg, rgba(21, 29, 41, 0.96), rgba(14, 21, 32, 0.9));
  border-bottom: 1px solid var(--border);
}

.results-header h2,
.results-header p {
  margin: 0;
}

.results-header h2 {
  font-family: "Space Grotesk", "IBM Plex Sans", sans-serif;
  font-size: 1.45rem;
  letter-spacing: -0.03em;
}

.results-header p {
  color: var(--muted);
}

.table-wrap {
  overflow-x: auto;
}

.results {
  width: 100%;
  border-collapse: collapse;
}

.results th,
.results td {
  padding: 0.95rem 1rem;
  text-align: left;
  vertical-align: top;
  border-bottom: 1px solid rgba(142, 169, 196, 0.09);
}

.results th {
  position: sticky;
  top: 0;
  background: rgba(18, 26, 38, 0.96);
  color: var(--muted);
  font-size: 0.82rem;
  letter-spacing: 0.06em;
  text-transform: uppercase;
}

.results tbody tr:nth-child(even) {
  background: rgba(255, 255, 255, 0.025);
}

.results tbody tr:hover {
  background: rgba(242, 143, 82, 0.08);
}

.mono {
  font-family: "IBM Plex Mono", "SFMono-Regular", Consolas, monospace;
  font-size: 0.92rem;
}

.empty-state {
  padding: 2rem 1.4rem 2.2rem;
  color: var(--muted);
}

@media (max-width: 860px) {
  .meta {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 640px) {
  .page {
    width: min(100% - 1rem, 1180px);
    padding-top: 1rem;
  }

  .hero-inner,
  .results-header {
    padding: 1.1rem;
  }

  .search-form {
    grid-template-columns: 1fr;
  }

  .results-header {
    align-items: start;
    flex-direction: column;
  }

  .results th,
  .results td {
    padding: 0.8rem 0.75rem;
  }
}
</style>
</head>
<body>
<div class="page">
  <section class="hero">
    <div class="hero-inner">
      <div class="eyebrow">Open Wine Components</div>
      <h1>Search the UMU compatibility database.</h1>
      <p class="lead">
        Look up titles, codenames, acronyms, or executable strings and inspect the mapped UMU IDs used for Protonfixes integration across supported stores.
      </p>

      <form class="search-form" method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
        <input
          class="search-input"
          type="text"
          name="search"
          placeholder="Try Syberia, Catnip, or umu-397540"
          value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>"
          required
        >
        <input class="search-button" type="submit" value="Search Database">
      </form>

      <div class="meta">
        <div class="meta-card">
          <strong>Contribute</strong>
          <span>Can’t find a game? Help expand the database and improve title coverage.</span>
        </div>
        <div class="meta-card">
          <strong>Data Source</strong>
          <a href="https://github.com/Open-Wine-Components/umu-database/blob/main/umu-database.csv">umu-database.csv</a>
        </div>
        <div class="meta-card">
          <strong>Guidelines</strong>
          <a href="https://github.com/Open-Wine-Components/umu-database/blob/main/README.md#rules-for-adding-umu-id-entries">Rules for adding UMU ID entries</a>
        </div>
      </div>
    </div>
  </section>

  <section class="results-panel">
    <?php if (isset($results)) { ?>
      <div class="results-header">
        <div>
          <h2>Results for "<?php echo htmlspecialchars($_POST["search"]); ?>"</h2>
          <p><?php echo $stmt->rowCount(); ?> matching entr<?php echo $stmt->rowCount() === 1 ? 'y' : 'ies'; ?></p>
        </div>
        <p>Database imports from GitHub are refreshed hourly.</p>
      </div>
      <div class="table-wrap">
        <table class="results">
          <thead>
            <tr>
              <th>#</th>
              <th>Title</th>
              <th>UMU ID</th>
              <th>Store</th>
              <th>Codename</th>
              <th>EXE String</th>
              <th>Acronym</th>
              <th>Notes</th>
              <th>Protonfixes Script</th>
            </tr>
          </thead>
          <tbody>
          <?php
          $counter = 1;
          foreach ($results as $result) {
            echo "<tr>";
            echo "<td>" . $counter . "</td>";
            echo "<td>" . htmlspecialchars($result['title']) . "</td>";
            echo "<td class=\"mono\">" . htmlspecialchars($result['umu_id']) . "</td>";
            echo "<td>" . htmlspecialchars($result['store']) . "</td>";
            echo "<td class=\"mono\">" . htmlspecialchars($result['codename']) . "</td>";
            echo "<td class=\"mono\">" . htmlspecialchars($result['exe_string'] ?? '') . "</td>";
            echo "<td>" . htmlspecialchars($result['acronym']) . "</td>";
            echo "<td>" . htmlspecialchars($result['notes']) . "</td>";

            if (htmlspecialchars($result['store']) == 'none') {
              $result['store'] = 'umu';
            }
            $fileContents = file_get_contents("https://raw.githubusercontent.com/Open-Wine-Components/umu-protonfixes/master/gamefixes-" . htmlspecialchars($result['store']) . "/" . htmlspecialchars($result['umu_id']) . ".py");
            if (!$fileContents) {
              echo "<td>None</td>";
            } else if (strpos($fileContents, 'gamefixes-steam') != false) {
              echo "<td><a href=\"https://github.com/Open-Wine-Components/umu-protonfixes/blob/master/" . str_replace('../', '', $fileContents) . "\">" . htmlspecialchars($result['umu_id']) . "</a></td>";
            } else {
              echo "<td><a href=\"https://github.com/Open-Wine-Components/umu-protonfixes/blob/master/gamefixes-" . htmlspecialchars($result['store']) . "/" . htmlspecialchars($result['umu_id']) . ".py\">" . htmlspecialchars($result['umu_id']) . "</a></td>";
            }
            echo "</tr>";
            $counter++;
          }
          ?>
          </tbody>
        </table>
      </div>
    <?php } else { ?>
      <div class="empty-state">
        Enter a search term above to browse matching UMU database entries.
      </div>
    <?php } ?>
  </section>
</div>
</body>
</html>

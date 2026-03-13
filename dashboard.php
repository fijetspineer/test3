<?php
/**
 * Dashboard – Persönlicher Speicherbereich
 *
 * Zeigt Dateien des eingeloggten Benutzers an.
 * Bietet Upload-Formular und Speicherplatz-Anzeige.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/storage.php';

requireLogin();

$userId = currentUserId();
$title  = 'Meine Dateien – BSN Klassen-Plattform';

// Aktueller Ordner-Filter
$currentFolder = isset($_GET['folder']) ? sanitizeFolderName($_GET['folder']) : '';

// Speicherplatz-Info
$used     = getUsedStorage($userId);
$limit    = getStorageLimit($userId);
$percent  = $limit > 0 ? round(($used / $limit) * 100, 1) : 0;
$barClass = $percent > 90 ? 'danger' : ($percent > 70 ? 'warning' : '');

// Dateien und Ordner laden
$files   = getUserFiles($userId, $currentFolder);
$folders = getUserFolders($userId);

include __DIR__ . '/includes/header.php';
?>

<?php
// Flash-Nachrichten anzeigen
if (!empty($_SESSION['flash_error'])):
?>
    <div class="alert alert-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>
<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<div class="card">
    <h2>📁 Meine Dateien</h2>

    <!-- Speicherplatz-Anzeige -->
    <div class="storage-bar">
        <div class="storage-bar-fill <?= $barClass ?>" style="width: <?= min($percent, 100) ?>%;"></div>
    </div>
    <div class="storage-info">
        <?= formatBytes($used) ?> von <?= formatBytes($limit) ?> belegt (<?= $percent ?>%)
    </div>

    <!-- Ordner-Navigation -->
    <div class="folder-list">
        <a href="dashboard.php" class="folder-tag <?= $currentFolder === '' ? 'active' : '' ?>">
            🏠 Hauptordner
        </a>
        <?php foreach ($folders as $f): ?>
            <a href="dashboard.php?folder=<?= urlencode($f) ?>"
               class="folder-tag <?= $currentFolder === $f ? 'active' : '' ?>">
                📂 <?= htmlspecialchars($f) ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Upload-Formular -->
    <form method="POST" action="upload.php" enctype="multipart/form-data" style="margin-bottom:1.5rem;">
        <div style="display:flex;gap:0.5rem;flex-wrap:wrap;align-items:flex-end;">
            <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0;">
                <label for="file">Datei hochladen</label>
                <input type="file" id="file" name="file" required>
            </div>
            <div class="form-group" style="min-width:150px;margin-bottom:0;">
                <label for="folder">Ordner (optional)</label>
                <input type="text" id="folder" name="folder"
                       value="<?= htmlspecialchars($currentFolder) ?>"
                       placeholder="z.B. Mathe">
            </div>
            <button type="submit" class="btn btn-success" style="margin-bottom:0.1rem;">⬆ Hochladen</button>
        </div>
    </form>

    <!-- Datei-Tabelle -->
    <?php if (empty($files)): ?>
        <div class="empty-state">
            <div class="icon">📭</div>
            <p>Keine Dateien <?= $currentFolder ? 'in diesem Ordner' : '' ?> vorhanden.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Dateiname</th>
                        <th>Größe</th>
                        <th>Hochgeladen</th>
                        <th>Aktionen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($files as $file): ?>
                        <tr>
                            <td>📄 <?= htmlspecialchars($file['filename']) ?></td>
                            <td><?= formatBytes($file['filesize']) ?></td>
                            <td><?= htmlspecialchars($file['uploaded_at']) ?></td>
                            <td>
                                <a href="download.php?id=<?= $file['id'] ?>" class="btn btn-secondary">⬇ Download</a>
                                <form method="POST" action="delete_file.php" class="inline-form"
                                      onsubmit="return confirm('Datei wirklich löschen?');">
                                    <input type="hidden" name="file_id" value="<?= $file['id'] ?>">
                                    <input type="hidden" name="folder" value="<?= htmlspecialchars($currentFolder) ?>">
                                    <button type="submit" class="btn btn-danger">🗑 Löschen</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

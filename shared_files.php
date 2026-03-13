<?php
/**
 * Freigegebene Dateien anzeigen
 *
 * Zeigt freigegebene Ordner anderer Benutzer an.
 * Nur freigegebene Ordner sind lesbar – der Rest ist geschützt.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/storage.php';

requireLogin();

$title  = 'Freigegebene Dateien – BSN Klassen-Plattform';
$userId = currentUserId();

// Freigegebene Ordner anderer Nutzer laden
$sharedFolders = getSharedFoldersFromOthers($userId);

// Falls ein bestimmter Ordner angezeigt werden soll
$viewOwner  = isset($_GET['owner'])  ? (int) $_GET['owner'] : 0;
$viewFolder = isset($_GET['folder']) ? sanitizeFolderName($_GET['folder']) : '';
$files      = [];

if ($viewOwner > 0 && $viewFolder !== '') {
    $files = getSharedFiles($viewOwner, $viewFolder);
}

include __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h2>🔗 Freigegebene Dateien</h2>
    <p style="color:#64748b;margin-bottom:1rem;">
        Hier sehen Sie die Ordner, die andere Benutzer zum Lesen freigegeben haben.
    </p>

    <?php if (empty($sharedFolders)): ?>
        <div class="empty-state">
            <div class="icon">📭</div>
            <p>Aktuell hat niemand Ordner freigegeben.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Benutzer</th>
                        <th>Ordner</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sharedFolders as $sf): ?>
                        <tr>
                            <td>👤 <?= htmlspecialchars($sf['username']) ?></td>
                            <td>📂 <?= htmlspecialchars($sf['folder']) ?></td>
                            <td>
                                <a href="shared_files.php?owner=<?= $sf['user_id'] ?>&folder=<?= urlencode($sf['folder']) ?>"
                                   class="btn btn-secondary">📖 Anzeigen</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php if ($viewOwner > 0 && $viewFolder !== ''): ?>
<div class="card">
    <h3>📂 Dateien in "<?= htmlspecialchars($viewFolder) ?>"</h3>

    <?php if (empty($files)): ?>
        <div class="alert alert-info">
            Dieser Ordner ist leer oder nicht freigegeben.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Dateiname</th>
                        <th>Größe</th>
                        <th>Hochgeladen</th>
                        <th>Aktion</th>
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
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>

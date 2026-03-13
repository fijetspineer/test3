<?php
/**
 * Freigaben verwalten
 *
 * Benutzer kann eigene Ordner zum Lesen für andere User freigeben
 * oder Freigaben widerrufen.
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/storage.php';

requireLogin();

$title  = 'Freigaben verwalten – BSN Klassen-Plattform';
$userId = currentUserId();
$error   = '';
$success = '';

// Ordner freigeben
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'share') {
        $folder = $_POST['folder'] ?? '';
        $result = shareFolder($userId, $folder);
        if ($result === true) {
            $success = 'Ordner erfolgreich freigegeben.';
        } else {
            $error = $result;
        }
    } elseif ($_POST['action'] === 'unshare') {
        $folder = $_POST['folder'] ?? '';
        $result = unshareFolder($userId, $folder);
        if ($result === true) {
            $success = 'Freigabe erfolgreich aufgehoben.';
        } else {
            $error = $result;
        }
    }
}

$folders = getUserFolders($userId);
$shares  = getUserShares($userId);
$sharedFolderNames = array_column($shares, 'folder');

include __DIR__ . '/includes/header.php';
?>

<div class="card">
    <h2>⚙️ Freigaben verwalten</h2>
    <p style="color:#64748b;margin-bottom:1rem;">
        Geben Sie Ordner zum Lesen für andere Benutzer frei.
        Freigegebene Ordner können von allen anderen Nutzern eingesehen werden.
    </p>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <!-- Neuen Ordner freigeben -->
    <h3>Ordner freigeben</h3>
    <?php
    $unsharableFolders = array_diff($folders, $sharedFolderNames);
    if (empty($unsharableFolders)):
    ?>
        <div class="alert alert-info">
            Alle Ihre Ordner sind bereits freigegeben oder Sie haben noch keine Ordner mit Dateien.
            <br>Laden Sie zuerst Dateien in einen Ordner hoch.
        </div>
    <?php else: ?>
        <form method="POST" action="share.php" style="margin-bottom:1.5rem;">
            <input type="hidden" name="action" value="share">
            <div style="display:flex;gap:0.5rem;align-items:flex-end;">
                <div class="form-group" style="flex:1;margin-bottom:0;">
                    <label for="folder">Ordner auswählen</label>
                    <select id="folder" name="folder" required>
                        <option value="">-- Ordner wählen --</option>
                        <?php foreach ($unsharableFolders as $f): ?>
                            <option value="<?= htmlspecialchars($f) ?>"><?= htmlspecialchars($f) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success" style="margin-bottom:0.1rem;">🔗 Freigeben</button>
            </div>
        </form>
    <?php endif; ?>

    <!-- Aktive Freigaben -->
    <h3>Aktive Freigaben</h3>
    <?php if (empty($shares)): ?>
        <div class="empty-state">
            <div class="icon">🔒</div>
            <p>Sie haben noch keine Ordner freigegeben.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Ordner</th>
                        <th>Freigegeben am</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shares as $share): ?>
                        <tr>
                            <td>📂 <?= htmlspecialchars($share['folder']) ?></td>
                            <td><?= htmlspecialchars($share['created_at']) ?></td>
                            <td>
                                <form method="POST" action="share.php" class="inline-form"
                                      onsubmit="return confirm('Freigabe wirklich aufheben?');">
                                    <input type="hidden" name="action" value="unshare">
                                    <input type="hidden" name="folder" value="<?= htmlspecialchars($share['folder']) ?>">
                                    <button type="submit" class="btn btn-danger">🔒 Aufheben</button>
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

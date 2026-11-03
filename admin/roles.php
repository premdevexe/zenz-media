<?php
// ============================================================================
// Enterprise Role-Based Access Control (RBAC) & Permission Console
// ============================================================================

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Include database connection & security
require_once __DIR__ . '/../app/db.php';
require_once __DIR__ . '/../app/security.php';
require_once __DIR__ . '/../app/AttendanceHelper.php';

Security::init($pdo);
Security::initSecureSession();

// Check if admin is logged in
if (!Security::isAuthenticated()) {
    header("Location: ../admin");
    exit();
}

$pageTitle = 'Role & Permission Management';
$currentPage = 'roles';

// Safe resolution of admin ID
$adminId = $_SESSION['admin_id'] ?? null;
Security::requirePermission('roles.view', $adminId, '../admin/dashboard');

// Flash message handling
$message = '';
$messageType = '';

// Check permission to view/manage roles
$canManageRoles = Security::hasPermission('roles.manage', $adminId);
$isSuperAdminUser = Security::isSuperAdmin($adminId);
$currentUserPermSlugs = Security::getAdminPermissions($adminId);

// ----------------------------------------------------------------------------
// Process POST Actions: Edit Role and Permissions
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if (!isset($_POST['csrf_token']) || !Security::validateCSRFToken($_POST['csrf_token'], 'roles_action')) {
        $_SESSION['roles_flash'] = 'Invalid or expired security token. Please refresh and try again.';
        $_SESSION['roles_flash_type'] = 'error';
    } elseif (!$canManageRoles) {
        $_SESSION['roles_flash'] = 'Access Denied: You do not possess the roles.manage privilege required to modify system roles.';
        $_SESSION['roles_flash_type'] = 'error';
    } else {
        if ($action === 'edit_role') {
            $roleId = isset($_POST['role_id']) ? (int)$_POST['role_id'] : 0;
            $roleName = trim($_POST['role_name'] ?? '');
            $roleDesc = trim($_POST['role_description'] ?? '');
            $selectedPerms = isset($_POST['permissions']) && is_array($_POST['permissions']) ? array_map('intval', $_POST['permissions']) : [];

            // Fetch target role
            $stmt = $pdo->prepare("SELECT * FROM roles WHERE id = ?");
            $stmt->execute([$roleId]);
            $targetRole = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$targetRole) {
                $_SESSION['roles_flash'] = 'The selected role does not exist.';
                $_SESSION['roles_flash_type'] = 'error';
            } elseif (empty($roleName)) {
                $_SESSION['roles_flash'] = 'Role Name cannot be empty.';
                $_SESSION['roles_flash_type'] = 'error';
            } elseif (strlen($roleName) < 3 || strlen($roleName) > 50) {
                $_SESSION['roles_flash'] = 'Role Name must be between 3 and 50 characters.';
                $_SESSION['roles_flash_type'] = 'error';
            } else {
                // Check duplicate role name against other roles
                $dupStmt = $pdo->prepare("SELECT id FROM roles WHERE (name = ? OR slug = ?) AND id != ?");
                $dupStmt->execute([$roleName, Security::slugify($roleName), $roleId]);
                if ($dupStmt->fetch()) {
                    $_SESSION['roles_flash'] = 'A role with this name or slug already exists. Please choose a unique name.';
                    $_SESSION['roles_flash_type'] = 'error';
                } else {
                    // Protection of critical system roles: Super Admin
                    if ($targetRole['slug'] === 'super_admin') {
                        if (!$isSuperAdminUser) {
                            $_SESSION['roles_flash'] = 'Access Denied: Only Super Admin can modify the Super Admin role.';
                            $_SESSION['roles_flash_type'] = 'error';
                            header('Location: roles');
                            exit();
                        }
                        // Super Admin retains all system permissions
                        $allPermIds = $pdo->query("SELECT id FROM permissions")->fetchAll(PDO::FETCH_COLUMN);
                        $selectedPerms = array_map('intval', $allPermIds);
                    } elseif (!$isSuperAdminUser) {
                        // Non-Super Admins can only assign permissions they themselves possess
                        $allPermRows = $pdo->query("SELECT id, slug FROM permissions")->fetchAll(PDO::FETCH_ASSOC);
                        $actorAllowedIds = [];
                        foreach ($allPermRows as $pRow) {
                            if (in_array($pRow['slug'], $currentUserPermSlugs, true)) {
                                $actorAllowedIds[] = (int)$pRow['id'];
                            }
                        }
                        $actorAllowedSet = array_flip($actorAllowedIds);
                        $filteredPerms = [];
                        foreach ($selectedPerms as $pId) {
                            if (isset($actorAllowedSet[$pId])) {
                                $filteredPerms[] = $pId;
                            }
                        }
                        $selectedPerms = $filteredPerms;
                    }

                    // Database Transaction
                    try {
                        $pdo->beginTransaction();

                        // 1. Update role name & description (do not alter slug for system roles)
                        $updateStmt = $pdo->prepare("UPDATE roles SET name = ?, description = ?, updated_at = NOW() WHERE id = ?");
                        $updateStmt->execute([$roleName, $roleDesc ?: null, $roleId]);

                        // 2. Remove old permissions for this role
                        $delStmt = $pdo->prepare("DELETE FROM role_permissions WHERE role_id = ?");
                        $delStmt->execute([$roleId]);

                        // 3. Insert newly assigned permissions
                        if (!empty($selectedPerms)) {
                            // Validate permission IDs against valid permissions table
                            $validPermStmt = $pdo->query("SELECT id FROM permissions");
                            $validPermIds = $validPermStmt->fetchAll(PDO::FETCH_COLUMN);
                            $validPermSet = array_flip($validPermIds);

                            $insPermStmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");
                            foreach ($selectedPerms as $pId) {
                                if (isset($validPermSet[$pId])) {
                                    $insPermStmt->execute([$roleId, $pId]);
                                }
                            }
                        }

                        $pdo->commit();

                        Security::logAudit($adminId, 'edit_role', 'roles', $roleId, [
                            'role_name' => $roleName,
                            'permissions_count' => count($selectedPerms)
                        ]);

                        $_SESSION['roles_flash'] = "Role \"{$roleName}\" and permissions updated successfully.";
                        $_SESSION['roles_flash_type'] = 'success';
                    } catch (PDOException $e) {
                        $pdo->rollBack();
                        error_log("Edit role error: " . $e->getMessage());
                        $_SESSION['roles_flash'] = 'Database transaction failed while saving role.';
                        $_SESSION['roles_flash_type'] = 'error';
                    }
                }
            }
        }
    }
    header('Location: roles');
    exit();
}

// Retrieve flash message
$flashMessage = $_SESSION['roles_flash'] ?? '';
$flashType = $_SESSION['roles_flash_type'] ?? '';
unset($_SESSION['roles_flash'], $_SESSION['roles_flash_type']);

// Fetch all roles with assigned user counts and permission counts
try {
    $rolesQuery = "SELECT r.*, 
        COUNT(DISTINCT rp.permission_id) AS permissions_count,
        COUNT(DISTINCT ar.admin_id) AS admins_count,
        COUNT(DISTINCT e.id) AS employees_count
        FROM roles r
        LEFT JOIN role_permissions rp ON r.id = rp.role_id
        LEFT JOIN admin_roles ar ON r.id = ar.role_id
        LEFT JOIN employees e ON r.id = e.role_id
        GROUP BY r.id
        ORDER BY r.id ASC";
    $roles = $pdo->query($rolesQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all available permissions grouped by module
    $allPerms = $pdo->query("SELECT * FROM permissions ORDER BY module ASC, name ASC")->fetchAll(PDO::FETCH_ASSOC);
    $permsByModule = [];
    foreach ($allPerms as $perm) {
        $module = ucfirst($perm['module']);
        $permsByModule[$module][] = $perm;
    }

    // Fetch role permissions mapping: [role_id => [perm_id, ...]]
    $rpStmt = $pdo->query("SELECT role_id, permission_id FROM role_permissions");
    $rolePermMap = [];
    while ($row = $rpStmt->fetch(PDO::FETCH_ASSOC)) {
        $rolePermMap[$row['role_id']][] = (int)$row['permission_id'];
    }
} catch (PDOException $e) {
    error_log("Error fetching roles: " . $e->getMessage());
    $roles = [];
    $permsByModule = [];
    $rolePermMap = [];
}

$csrfToken = Security::generateCSRFToken('roles_action');

include 'header.php';
?>

<style>
    .roles-page-wrapper {
        display: flex;
        flex-direction: column;
        gap: 24px;
        padding-bottom: 60px;
    }
    .role-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 320px), 1fr));
        gap: 20px;
    }
    .role-card {
        background: var(--adm-surface);
        border: 1px solid var(--adm-border);
        border-radius: var(--adm-radius-card);
        box-shadow: var(--adm-shadow-card);
        padding: 24px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
        min-width: 0;
        overflow-wrap: break-word;
        word-break: break-word;
    }
    .role-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -4px rgba(15, 23, 42, 0.1);
    }
    .role-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }
    .role-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.22rem;
        font-weight: 800;
        color: var(--adm-text-main);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .role-slug-badge {
        display: inline-block;
        font-family: monospace;
        font-size: 0.76rem;
        color: var(--adm-muted);
        background: var(--adm-surface-subtle);
        padding: 2px 8px;
        border-radius: 6px;
        margin-top: 4px;
    }
    .role-desc {
        font-size: 0.88rem;
        color: var(--adm-text-body);
        line-height: 1.5;
        margin-bottom: 18px;
        min-height: 42px;
    }
    .role-meta-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 12px 0;
        border-top: 1px solid var(--adm-border-subtle);
        border-bottom: 1px solid var(--adm-border-subtle);
        margin-bottom: 18px;
        font-size: 0.82rem;
        color: var(--adm-muted);
    }
    .role-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }
    .role-action-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
    }
    .btn-edit-role {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 18px;
        border-radius: var(--adm-radius-pill);
        background: var(--adm-brand-light);
        border: 1px solid var(--adm-brand-border);
        color: var(--adm-brand);
        font-size: 0.86rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-edit-role:hover {
        background: var(--adm-brand);
        color: #ffffff;
        transform: translateY(-1px);
    }

    /* Badges */
    .badge-saas {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: var(--adm-radius-pill, 9999px);
        font-size: 0.74rem;
        font-weight: 700;
        line-height: 1.4;
        white-space: nowrap;
    }
    .badge-clean {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }
    .badge-info {
        background: rgba(59, 130, 246, 0.12);
        color: #2563eb;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }
    .badge-purple {
        background: rgba(139, 92, 246, 0.12);
        color: #7c3aed;
        border: 1px solid rgba(139, 92, 246, 0.25);
    }
    .badge-danger {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    /* Alerts */
    .alert-banner {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 18px;
        border-radius: var(--adm-radius-md, 10px);
        font-size: 0.88rem;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .alert-success {
        background: rgba(16, 185, 129, 0.12);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: #059669;
    }
    .alert-error {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: #dc2626;
    }

    /* Form and Panel Styles */
    .panel-header-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--adm-text-main);
        margin: 0;
    }
    .panel-header-subtitle {
        font-size: 0.82rem;
        color: var(--adm-muted);
        margin: 4px 0 0 0;
    }
    .form-group-item {
        margin-bottom: 16px;
    }
    .form-group-item label {
        display: block;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--adm-text-main);
        margin-bottom: 6px;
    }
    .input-with-icon-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }
    .input-prefix-icon {
        position: absolute;
        left: 14px;
        color: var(--adm-muted);
        font-size: 0.88rem;
        pointer-events: none;
        z-index: 2;
    }
    .form-control-saas {
        width: 100%;
        height: 42px;
        box-sizing: border-box;
        padding: 10px 14px 10px 38px;
        font-size: 0.88rem;
        border-radius: var(--adm-radius-md);
        border: 1px solid var(--adm-border);
        background: var(--adm-bg);
        color: var(--adm-text-main);
        font-family: inherit;
        transition: all var(--adm-transition);
    }
    .form-control-saas:focus {
        outline: none;
        border-color: var(--adm-brand);
        background: var(--adm-surface);
        box-shadow: 0 0 0 3px var(--adm-brand-light);
    }

    /* Mini Buttons */
    .mini-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        font-size: 0.76rem;
        font-weight: 700;
        border-radius: var(--adm-radius-sm);
        border: 1px solid var(--adm-border);
        background: var(--adm-surface);
        color: var(--adm-text-main);
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
        font-family: inherit;
        white-space: nowrap;
    }
    .mini-btn:hover {
        border-color: var(--adm-brand);
        color: var(--adm-brand);
        background: var(--adm-brand-light);
    }

    /* Modal Buttons */
    .modal-btn-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 12px;
    }
    .modal-btn-cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 9px 18px;
        font-size: 0.88rem;
        font-weight: 700;
        border-radius: var(--adm-radius-md);
        border: 1px solid var(--adm-border);
        background: var(--adm-surface);
        color: var(--adm-text-body);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .modal-btn-cancel:hover {
        background: var(--adm-surface-subtle);
        color: var(--adm-text-main);
    }
    .modal-btn-confirm {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 9px 20px;
        font-size: 0.88rem;
        font-weight: 700;
        border-radius: var(--adm-radius-md);
        border: 1px solid transparent;
        background: var(--adm-brand);
        color: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }
    .modal-btn-confirm:hover {
        background: var(--adm-brand-hover, #4f46e5);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }
    .modal-btn-confirm:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    /* Modal Styling */
    .role-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(9, 13, 22, 0.75);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
        z-index: 10000;
        padding: 20px;
    }
    .role-modal-dialog {
        background: var(--adm-surface);
        border: 1px solid var(--adm-border);
        border-radius: var(--adm-radius-card);
        max-width: 680px;
        width: 100%;
        max-height: 92vh;
        overflow-y: auto;
        padding: 28px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        animation: modalScaleIn 0.2s ease-out;
    }
    @keyframes modalScaleIn {
        from { transform: scale(0.95); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .perm-module-card {
        background: var(--adm-surface-subtle);
        border: 1px solid var(--adm-border-subtle);
        border-radius: var(--adm-radius-md);
        padding: 14px 16px;
        margin-bottom: 14px;
        transition: border-color 0.2s ease;
    }
    .perm-module-card:hover {
        border-color: var(--adm-border);
    }
    .perm-module-title {
        font-size: 0.88rem;
        font-weight: 700;
        color: var(--adm-text-main);
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .perm-checkbox-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 10px;
    }
    .perm-checkbox-item {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--adm-text-body);
        cursor: pointer;
        padding: 6px 8px;
        border-radius: var(--adm-radius-sm);
        transition: background-color 0.15s ease;
    }
    .perm-checkbox-item:hover {
        background: rgba(99, 102, 241, 0.06);
    }
    .perm-checkbox-item input {
        margin-top: 3px;
        accent-color: var(--adm-brand);
        width: 15px;
        height: 15px;
        cursor: pointer;
    }
    @media (max-width: 768px) {
        .perm-checkbox-grid { grid-template-columns: 1fr; }
        .role-grid { grid-template-columns: 1fr; }
        .role-meta-row { flex-wrap: wrap; }
    }
    @media (max-width: 480px) {
        .roles-page-wrapper { gap: 16px; }
        .role-card { padding: 16px; }
        .role-modal-dialog { padding: 16px; width: 100%; max-width: 100%; }
        .perm-module-card { padding: 12px; }
        .modal-btn-row { flex-direction: column-reverse; width: 100%; }
        .modal-btn-cancel, .modal-btn-confirm { width: 100%; justify-content: center; }
    }
</style>

<div class="roles-page-wrapper">

    <!-- Top Ribbon -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 800; color: var(--adm-text-main); margin-bottom: 4px;">
                <i class="fa-solid fa-user-shield" style="color: var(--adm-brand);"></i> Role &amp; Permission Management
            </h1>
            <p style="font-size: 0.92rem; color: var(--adm-muted);">
                Granular Role-Based Access Control (RBAC) &bull; Configure administrative authorities, module permissions, and security roles.
            </p>
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <span class="badge-saas badge-clean">
                <i class="fa-solid fa-shield-halved"></i> <?php echo count($roles); ?> Defined Roles
            </span>
            <span class="badge-saas badge-clean">
                <i class="fa-solid fa-key"></i> <?php echo count($allPerms); ?> Available Permissions
            </span>
        </div>
    </div>

    <!-- Feedback Toast Alert -->
    <?php if (!empty($flashMessage)): ?>
        <div class="alert-banner alert-<?php echo $flashType === 'success' ? 'success' : 'error'; ?>" role="alert" style="margin: 0;">
            <i class="fa-solid <?php echo $flashType === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
            <span><?php echo htmlspecialchars($flashMessage); ?></span>
        </div>
    <?php endif; ?>

    <!-- Roles Grid -->
    <div class="role-grid">
        <?php foreach ($roles as $r): ?>
            <?php 
                $rId = (int)$r['id'];
                $isSuperAdmin = ($r['slug'] === 'super_admin');
                $isSystem = !empty($r['is_system']);
                $assignedPerms = $rolePermMap[$rId] ?? [];
            ?>
            <div class="role-card">
                <div>
                    <div class="role-card-header">
                        <div>
                            <div class="role-title">
                                <?php if ($isSuperAdmin): ?>
                                    <i class="fa-solid fa-crown" style="color: #eab308;"></i>
                                <?php elseif ($isSystem): ?>
                                    <i class="fa-solid fa-shield" style="color: var(--adm-brand);"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-id-badge" style="color: #06b6d4;"></i>
                                <?php endif; ?>
                                <span><?php echo htmlspecialchars($r['name']); ?></span>
                            </div>
                            <span class="role-slug-badge"><?php echo htmlspecialchars($r['slug']); ?></span>
                        </div>

                        <div>
                            <?php if ($isSystem): ?>
                                <span class="badge-saas badge-purple" style="font-size: 0.7rem;">
                                    <i class="fa-solid fa-lock"></i> System Role
                                </span>
                            <?php else: ?>
                                <span class="badge-saas badge-clean" style="font-size: 0.7rem;">
                                    Custom Role
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <p class="role-desc">
                        <?php echo htmlspecialchars($r['description'] ?: 'No description provided for this role.'); ?>
                    </p>
                </div>

                <div>
                    <div class="role-meta-row">
                        <div class="role-meta-item">
                            <i class="fa-solid fa-users" style="color: var(--adm-brand);"></i>
                            <span><?php echo ((int)$r['admins_count'] + (int)($r['employees_count'] ?? 0)); ?> Assigned</span>
                        </div>
                        <div class="role-meta-item">
                            <i class="fa-solid fa-key" style="color: #059669;"></i>
                            <span><?php echo (int)$r['permissions_count']; ?> Permissions</span>
                        </div>
                    </div>

                    <div class="role-action-row">
                        <?php if ($canManageRoles): ?>
                            <?php if ($isSuperAdmin && !$isSuperAdminUser): ?>
                                <span class="badge-saas" style="font-size: 0.8rem; color: #b45309; background: rgba(234, 179, 8, 0.12); border: 1px solid rgba(234, 179, 8, 0.3); padding: 6px 12px; border-radius: var(--adm-radius-pill);">
                                    <i class="fa-solid fa-lock"></i> Protected Root Role
                                </span>
                            <?php else: ?>
                                <button type="button" class="btn-edit-role" onclick='openEditRoleModal(<?php echo htmlspecialchars(json_encode([
                                    'id' => $r['id'],
                                    'name' => $r['name'],
                                    'slug' => $r['slug'],
                                    'description' => $r['description'] ?? '',
                                    'is_system' => $r['is_system'],
                                    'permissions' => $assignedPerms
                                ]), ENT_QUOTES, 'UTF-8'); ?>)'>
                                    <i class="fa-solid fa-pen-to-square"></i> Edit Role &amp; Permissions
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <span style="font-size: 0.8rem; color: var(--adm-muted);"><i class="fa-solid fa-lock"></i> Read-Only</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<!-- Edit Role Modal -->
<div class="role-modal-overlay" id="editRoleModal" role="dialog" aria-modal="true" aria-labelledby="editRoleModalTitle">
    <div class="role-modal-dialog">
        <form method="POST" action="" id="editRoleForm" onsubmit="return validateRoleForm(event)">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
            <input type="hidden" name="action" value="edit_role">
            <input type="hidden" name="role_id" id="editRoleId" value="">

            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px;">
                <div>
                    <h3 class="panel-header-title" id="editRoleModalTitle" style="font-size:1.3rem;">
                        <i class="fa-solid fa-pen-to-square" style="color:var(--adm-brand);"></i> Edit Role &amp; Permissions
                    </h3>
                    <p class="panel-header-subtitle" id="editRoleModalSub">Modify role credentials and toggle authorized module permissions.</p>
                </div>
                <button type="button" class="modal-btn-cancel" style="padding:4px 10px; font-size:0.85rem;" onclick="closeEditRoleModal()">&times;</button>
            </div>

            <div id="superAdminNotice" style="display:none; background:rgba(234, 179, 8, 0.15); border:1px solid rgba(234, 179, 8, 0.4); color:#b45309; padding:10px 14px; border-radius:var(--adm-radius-md); font-size:0.84rem; font-weight:600; margin-bottom:16px;">
                <i class="fa-solid fa-shield-halved"></i> <strong>Critical System Role:</strong> The <code>super_admin</code> role retains essential system security privileges to prevent administrative lockout.
            </div>

            <div class="form-group-item">
                <label for="editRoleName">Role Name *</label>
                <div class="input-with-icon-wrap">
                    <i class="fa-solid fa-shield-halved input-prefix-icon"></i>
                    <input type="text" id="editRoleName" name="role_name" class="form-control-saas" required minlength="3" maxlength="50" placeholder="e.g. Operations Manager">
                </div>
            </div>

            <div class="form-group-item">
                <label for="editRoleDescription">Role Description</label>
                <div class="input-with-icon-wrap">
                    <i class="fa-solid fa-align-left input-prefix-icon"></i>
                    <input type="text" id="editRoleDescription" name="role_description" class="form-control-saas" maxlength="255" placeholder="Summary of responsibilities and permissions...">
                </div>
            </div>

            <div style="margin-top:20px; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                <h4 style="font-size:0.95rem; font-weight:800; color:var(--adm-text-main); margin:0;">
                    <i class="fa-solid fa-key" style="color:var(--adm-brand);"></i> Permitted Module Permissions
                </h4>
                <div style="display:flex; gap:8px;">
                    <button type="button" class="mini-btn" onclick="toggleAllPerms(true)"><i class="fa-solid fa-check-double"></i> Select All</button>
                    <button type="button" class="mini-btn" onclick="toggleAllPerms(false)"><i class="fa-solid fa-xmark"></i> Deselect All</button>
                </div>
            </div>

            <!-- Permissions Grouped by Module -->
            <div id="permissionsContainer">
                <?php foreach ($permsByModule as $moduleName => $pList): ?>
                    <div class="perm-module-card">
                        <div class="perm-module-title">
                            <span><i class="fa-solid fa-folder-tree" style="color:var(--adm-brand); margin-right:6px;"></i> <?php echo htmlspecialchars($moduleName); ?></span>
                            <span style="font-size:0.75rem; color:var(--adm-muted);"><?php echo count($pList); ?> Actions</span>
                        </div>
                        <div class="perm-checkbox-grid">
                            <?php foreach ($pList as $p): ?>
                                <?php 
                                    $canAssignThisPerm = $isSuperAdminUser || in_array($p['slug'], $currentUserPermSlugs, true);
                                ?>
                                <label class="perm-checkbox-item" <?php echo !$canAssignThisPerm ? 'style="opacity:0.55; cursor:not-allowed;" title="You do not possess this privilege and cannot grant it."' : ''; ?>>
                                    <input type="checkbox" name="permissions[]" value="<?php echo (int)$p['id']; ?>" class="role-perm-check" id="perm_<?php echo (int)$p['id']; ?>" <?php echo !$canAssignThisPerm ? 'disabled' : ''; ?>>
                                    <span>
                                        <strong><?php echo htmlspecialchars($p['name']); ?></strong>
                                        <small style="display:block; color:var(--adm-muted); font-size:0.72rem;"><?php echo htmlspecialchars($p['description'] ?: $p['slug']); ?></small>
                                    </span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div id="roleModalErrorBox" class="alert-banner alert-error" style="display:none; margin-top:14px;"></div>

            <div class="modal-btn-row" style="margin-top:24px;">
                <button type="button" class="modal-btn-cancel" onclick="closeEditRoleModal()">Cancel</button>
                <button type="submit" id="btnSaveRole" class="modal-btn-confirm">
                    <i class="fa-solid fa-floppy-disk"></i> Save Role &amp; Permissions
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const editModal = document.getElementById('editRoleModal');

    function openEditRoleModal(role) {
        if (!editModal) return;
        document.getElementById('editRoleId').value = role.id;
        document.getElementById('editRoleName').value = role.name;
        document.getElementById('editRoleDescription').value = role.description || '';
        document.getElementById('roleModalErrorBox').style.display = 'none';

        // Super Admin Protection Banner
        const isSuper = (role.slug === 'super_admin');
        const superNotice = document.getElementById('superAdminNotice');
        if (superNotice) {
            superNotice.style.display = isSuper ? 'block' : 'none';
        }

        // Uncheck all permissions first
        document.querySelectorAll('.role-perm-check').forEach(cb => cb.checked = false);

        // Check assigned permissions
        if (Array.isArray(role.permissions)) {
            role.permissions.forEach(pId => {
                const cb = document.getElementById('perm_' + pId);
                if (cb) cb.checked = true;
            });
        }

        editModal.style.display = 'flex';
    }

    function closeEditRoleModal() {
        if (editModal) editModal.style.display = 'none';
    }

    function toggleAllPerms(checked) {
        document.querySelectorAll('.role-perm-check:not([disabled])').forEach(cb => cb.checked = checked);
    }

    function validateRoleForm(e) {
        const name = document.getElementById('editRoleName')?.value.trim();
        const errBox = document.getElementById('roleModalErrorBox');
        if (!name || name.length < 3) {
            e.preventDefault();
            if (errBox) {
                errBox.style.display = 'flex';
                errBox.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> Role name must be at least 3 characters.';
            } else {
                alert('Role name must be at least 3 characters.');
            }
            return false;
        }

        const btn = document.getElementById('btnSaveRole');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Saving Role...';
        }
        return true;
    }

    // Modal click and key listeners
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeEditRoleModal();
    });

    editModal?.addEventListener('click', (e) => {
        if (e.target === editModal) closeEditRoleModal();
    });
</script>

<?php include 'footer.php'; ?>

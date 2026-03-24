<?php
function isAdmin() {
    return $_SESSION['role'] === 'Admin';
}

function isDeveloper() {
    return $_SESSION['role'] === 'Developer';
}

function canEditPrompts() {
    return isAdmin() || isDeveloper();
}

function canManageUsers() {
    return isAdmin();
}

function isOwner($prompt) {
    return isAdmin() || (isDeveloper() && $prompt['developer_id'] == $_SESSION['user_id']);
}
?>
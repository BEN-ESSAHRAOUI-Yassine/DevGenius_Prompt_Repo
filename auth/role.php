<?php
function isAdmin() {
    return $_SESSION['role'] === 'Admin';
}

function isDeveloper() {
    return $_SESSION['role'] === 'developer';
}

function canEditPrompts($ownerId = null){
    if(isAdmin()) return true;
    if(isDeveloper() && $ownerId !== null){
        return $ownerId == $_SESSION['user_id'];
    }
    return false;
}

function canCreatePrompt(){
    return isAdmin() || isDeveloper();
}

function canManageUsers() {
    return isAdmin();
}

function canManageCategories(){
    return isAdmin();
}

function isOwner($prompt) {
    return isAdmin() || (isDeveloper() && $prompt['developer_id'] == $_SESSION['user_id']);
}

function canModeratePrompts(){
    return isAdmin(); // deploy / reject
}
?>
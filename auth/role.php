<?php
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'Admin';
}

function isDevelopper() {
    return $_SESSION['role'] === 'Developper';
}

function canEditPrompts($ownerId = null){
    if(isAdmin()) return true;
    if(isDevelopper() && $ownerId !== null){
        return $ownerId == $_SESSION['user_id'];
    }
    return false;
}

function canCreatePrompt(){
    return isAdmin() || isDevelopper();
}

function canManageUsers() {
    return isAdmin();
}

function canManageCategories(){
    return isAdmin();
}

function isOwner($prompt) {
    return isAdmin() || (isDevelopper() && $prompt['user_id'] == $_SESSION['user_id']);
}

function canModeratePrompts(){
    return isAdmin(); // deploy / reject
}
?>
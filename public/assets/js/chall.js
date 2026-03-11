function toggleChallEdit(id) {
    const viewDiv = document.getElementById('chall-view-' + id);
    const editDiv = document.getElementById('chall-edit-' + id);
    if (!viewDiv || !editDiv) return;

    if (viewDiv.style.display === 'none') {
        viewDiv.style.display = 'block';
        editDiv.style.display = 'none';
    } else {
        viewDiv.style.display = 'none';
        editDiv.style.display = 'block';
    }
}
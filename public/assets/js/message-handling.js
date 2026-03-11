function toggleMsgEdit(msgId) {
    const viewDiv = document.getElementById('msg-view-' + msgId);
    const editForm = document.getElementById('msg-edit-' + msgId);
    
    // check để tránh lỗi nếu không tìm thấy phần tử DOM
    if (!viewDiv || !editForm) return;

    if (viewDiv.style.display === 'none') {
        viewDiv.style.display = 'block';
        editForm.style.display = 'none';
    } else {
        viewDiv.style.display = 'none';
        editForm.style.display = 'block';
    }
}
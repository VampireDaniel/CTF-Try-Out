const editUserModal = document.getElementById('editUserModal');
const resetPasswordModal = document.getElementById('resetPasswordModal');

const closeButtons = document.getElementsByClassName('close');
const editButtons = document.getElementsByClassName('edit-button');
const resetPasswordButtons = document.getElementsByClassName('reset-password-button');

Array.from(editButtons).forEach(button => {
    button.addEventListener('click', () => {
        const userId = button.getAttribute('data-user-id');
        const row = button.parentElement.parentElement;
        const name = row.cells[1].textContent;
        const username = row.cells[2].textContent;
        const email = row.cells[3].textContent;
        const role = row.cells[4].textContent;

        document.getElementById('edit_user_id').value = userId;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_username').value = username;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_role').value = role;

        editUserModal.style.display = 'block';
    });
});

Array.from(resetPasswordButtons).forEach(button => {
    button.addEventListener('click', () => {
        const userId = button.getAttribute('data-user-id');
        document.getElementById('reset_user_id').value = userId;

        resetPasswordModal.style.display = 'block';
    });
});

Array.from(closeButtons).forEach(span => {
    span.addEventListener('click', () => {
        editUserModal.style.display = 'none';
        resetPasswordModal.style.display = 'none';
    });
});

window.addEventListener('click', (event) => {
    if (event.target == editUserModal) {
        editUserModal.style.display = 'none';
    }
    if (event.target == resetPasswordModal) {
        resetPasswordModal.style.display = 'none';
    }
});

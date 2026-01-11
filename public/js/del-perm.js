const csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

document.querySelectorAll('.delete-perm-btn').forEach((btn) => {
    btn.onclick = async function () {
        event.preventDefault();
        console.log('Delete perm btn clicked');
        var perm_id = this.getAttribute('data-perm-id');
        console.log(perm_id);

        let response = await fetch(`/permissions_manager/delete/${perm_id}`, {
            method: 'DELETE',
            headers: {
            'Content-Type': 'application/json;charset=utf-8',
            'X-CSRF-TOKEN': csrf_token,
            },
        });
        if (response.ok === true) {
            var tr_selector = document.querySelector(`tr[data-perm-th-id="${perm_id}"]`);
            tr_selector.remove();
        }
    }
});
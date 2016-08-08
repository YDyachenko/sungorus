function deleteAccount(url, name) {
    $('#deleteModal .item-type').text('account');
    deleteItem(url, name);
    return false;
}

function deleteFolder(url, name) {
    $('#deleteModal .item-type').text('folder');
    deleteItem(url, name);
    return false;
}

function deleteItem(url, name) {
    $.get(url, function (data) {
        $('#deleteModal .name').text(name);
        
        $('#deleteModal .btn-confirm').on('click', function() {
            $.post(url, {token: data.token}, function (response) {
                if (response.location === undefined)
                    window.location.reload();
                else
                    window.location = response.location;
            });
        });
        $('#deleteModal').modal();
    });
}

function generatePassword() {
    var length   = 16;
    var charset  = "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    var password = "";
    
    for (var i = 0; i < length; i++) {
        var pos = Math.floor(Math.random() * charset.length);
        password += charset.charAt(pos);
    }

    return password;
}
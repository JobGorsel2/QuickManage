$(document).ready(function () {
    var originalName = '';

    function startEditing() {
        var nameSpan = $('#categoryName');
        if ($('#categoryInput').length) return; // already editing
        originalName = nameSpan.text();

        nameSpan.html('<input type="text" id="categoryInput" class="category_change_input d-inline-block"   value="' + $('<div>').text(originalName).html() + '">');

        $('#editIcon').hide();
        $('#saveIcon, #cancelIcon').show();
    }

    $('#editIcon').on('click', startEditing);
    $('#categoryName').on('click', startEditing);

    $('#cancelIcon').on('click', function () {
        $('#categoryName').text(originalName);
        $('#saveIcon, #cancelIcon').hide();
        $('#editIcon').show();
    });

    $(document).on('keydown', '#categoryInput', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $('#saveIcon').trigger('click');
        }
    });

    $('#saveIcon').on('click', function () {
        var newName = $('#categoryInput').val().trim();
        if (!newName) return;

        var categoryId = $('#categoryTitle').data('id');

        $.ajax({
            url: '/app-gallery/category/update/' + categoryId,
            type: 'PATCH',
            data: {
                category_name: newName,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                $('#categoryName').text(response.category_name);
                $('#saveIcon, #cancelIcon').hide();
                $('#editIcon').show();
            },
            error: function () {
                alert('Er ging iets mis bij het opslaan.');
            }
        });
    });
});
jQuery(document).ready(function($) {
    let deactivationButton = $("#deactivate-crud");
    let TableName = deactivationData.tableName;

    deactivationButton.on('click', function(e) {
        e.preventDefault();
        $('body').append(`
            <div id="confirm-popup" style="background-color: rgba(0, 0, 0, 0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">
                <div style="background-color: white; padding: 20px; border-radius: 5px; width: 300px; text-align: center;">
                    <h4>Do you want to delete the ${TableName} table?</h4>
                    <label><input type="radio" name="delete_table" value="yes"> Yes</label><br>
                    <label><input type="radio" name="delete_table" value="no"> No</label><br><br>
                    <button id="confirm-btn" style="margin-right: 10px;">Confirm</button>
                    <button id="cancel-btn">Cancel</button>
                </div>
            </div>
        `);

        $('#confirm-btn').on('click', function() {
            let deleteTable = $('input[name="delete_table"]:checked').val();

            if (deleteTable === undefined) {
                alert('Please select an option!');
                return;
            }

            // Send AJAX request
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'handle_delete_table',
                    delete_table: deleteTable,
                    nonce:deactivationData.nonce
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = deactivationButton.attr('href');
                    } else {
                        alert('Something went wrong!');
                    }
                }
            });
        });
        $('#cancel-btn').on('click', function() {
            $('#confirm-popup').remove();
        });
    });
});

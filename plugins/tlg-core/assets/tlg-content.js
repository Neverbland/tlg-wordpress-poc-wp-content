jQuery(document).ready(function ($) {
  $('#new-location').submit(function (event) {
    // prevent default
    event.preventDefault();

    const location = $(this).find('input[name="location"]').val();
    const nonce = $(this).find('input[name="nonce"]').val();

    $.ajax({
      url: tlg_content_ajax.ajax_url,
      type: 'POST',
      data: {
        action: 'tlg_content_new_location_handler',
        location,
        nonce,
      },
      success: function (response) {
        console.log('response: ', response);

        // Clear the location input.
        $('#new-location').find('input[name="location"]').val('');

        if (response.success) {
          // Append a success message to #tlg-content-new-location-log
          $('#tlg-content-new-location-log').append(
            `<p>Location ${location} added successfully.</p>`,
          );
        } else {
          // Append an error message to #tlg-content-new-location-log and print
          // response.data
          $('#tlg-content-new-location-log').append(
            `<p>Failed to add location ${location}. ${response.data ?? 'Something went wrong'}</p>`,
          );
        }
      },
    });
  });
});

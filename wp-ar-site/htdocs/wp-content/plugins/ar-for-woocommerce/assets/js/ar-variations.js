jQuery(document).ready(function($) {
    // Listen for the WooCommerce variation change event
    $('form.variations_form').on('found_variation', function(event, variation) {
        // Check if the variation ID exists
        if (variation && variation.variation_id) {
            var variationID = variation.variation_id;
            console.log('Selected variation ID:', variationID);

            // Find the button with the corresponding ID and trigger its click event
            var buttonSelector = '#ar_btn_' + variationID; // Construct the button selector
            var button = $(buttonSelector); // Select the button

            if (button.length) { // Check if the button exists
                button.trigger('click'); // Trigger the click event
                console.log('Button with ID ' + buttonSelector + ' clicked.');
            } else {
                console.warn('Button with ID ' + buttonSelector + ' does not exist.');
            }
        }
    });
});
(function (_, $) {
  $.ceEvent('on', 'ce.calculate_total_shipping.before_send_request', function (data, parent) {
    data.vendor_id = parent.find('input[name="vendor_id"]').first().val();
  });
  $.ceEvent('on', 'ce.stripe.instant_payment_get_request_data_post', function (paymentRequestData, paymentButton) {
    paymentRequestData.vendor_id = paymentButton.data('caStripeVendorId');
  });
})(Tygh, Tygh.$);
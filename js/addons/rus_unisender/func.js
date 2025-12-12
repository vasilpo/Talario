(function (_, $) {
  $(document).ready(function () {
    $('#button_send_sms').click(function () {
      var phone = $('#elm_profile_phone').val();
      var text_sms = $('#text_sms').val();
      $.ceAjax('request', fn_url("unisender.send_sms"), {
        data: {
          text_phone: phone,
          text_sms: text_sms,
          result_ids: 'content_message'
        }
      });
      $('#text_sms').val('');
    });
  });
})(Tygh, Tygh.$);
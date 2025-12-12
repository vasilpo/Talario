(function (_, $) {
  var isCheckoutScriptLoaded,
    validationLoop,
    isPlaceOrderAllowed,
    isRepayOrder,
    options,
    submitButtonId,
    applePay,
    applePayVersion = 14,
    applePayConfig,
    applePaySession,
    applePayPaymentEvent,
    orderId,
    orderTotal,
    paymentForm,
    googlePayDeffered,
    googlePayConfig,
    googlePayClient;
  var methods = {
    /**
     * Changes default 'Submit my order' button ID.
     * Submit button ID must be altered to prevent 'button_already_has_paypal_click_listener' warning.
     *
     * @param {string} buttonId Button ID
     * @returns {string} New button ID
     */
    setSubmitButtonId: function (buttonId) {
      var newButtonId = buttonId + '_' + Date.now();
      var $button = $('#' + buttonId);
      $button.attr('id', newButtonId);
      return newButtonId;
    },
    /**
     * Provides request to place an order.
     *
     * @param {jQuery} $paymentForm
     * @returns {{redirect_on_charge: string, is_ajax: number}}
     */
    getOrderPlacementRequest: function ($paymentForm) {
      var formData = {
        is_ajax: 1,
        custom_paypal_button: 1
      };
      var fields = $paymentForm.serializeArray();
      for (var i in fields) {
        formData[fields[i].name] = fields[i].value;
      }
      formData.result_ids = null;
      return formData;
    },
    /**
     * Renders payment buttons.
     *
     * @param {Object} params Payment form config
     */
    setupPaymentForm: function (params) {
      params = params || {};
      params.payment_form = params.payment_form || null;
      params.submit_button_id = params.submit_button_id || '';
      params.style = params.style || {};
      params.style.layout = params.style.layout || 'vertical';
      params.style.color = params.style.color || 'gold';
      params.style.height = params.style.height || 40;
      params.style.shape = params.style.shape || 'rect';
      params.style.label = params.style.label || 'pay';
      params.style.tagline = params.style.tagline || false;
      paymentForm = params.payment_form;
      methods.stopValidation();
      methods.createPaymentButtonsContainer(params.submit_button_id);
      paypal.Buttons({
        style: params.style,
        onInit: function (data, actions) {
          methods.forbidOrderPlacement(actions);
          methods.startValidation(paymentForm, actions);
        },
        onClick: function (data, actions) {
          paymentForm.ceFormValidator('checkFields', false);
        },
        createOrder: function (data, actions) {
          orderId = null;
          var deferredOrder = $.Deferred();
          methods.placeOrder(deferredOrder, paymentForm);
          return deferredOrder.promise().then(function (success) {
            orderId = success.order_id;
            return success.order_id_in_paypal;
          }, function (fail) {
            new Error(fail.error);
          });
        },
        onApprove: function (data, actions) {
          $.toggleStatusBox('show');
          methods.redirect(orderId, data.orderID);
        }
      }).render('#' + params.submit_button_id + '_container').catch(() => {});
      methods.applePayCheck().then(() => {
        applePay = paypal.Applepay();
        applePay.config().then(_applePayConfig => {
          if (_applePayConfig.isEligible) {
            $('#' + submitButtonId + '_applepay-container').html('<apple-pay-button id="' + submitButtonId + '_applepay_button" class="ty-paypal-checkout-apple-pay-button" buttonstyle="black" type="plain" locale="en">');
            $('#' + submitButtonId + '_applepay_button').on('click', methods.applePayHandleClicked);
            applePayConfig = _applePayConfig;
          }
        }).catch(applePayConfigError => {
          console.error('Error while fetching apple pay config');
          console.error(applePayConfigError);
        });
      }).catch(error => console.error(error));
      googlePayDeffered.done(() => {
        googlePayClient = new google.payments.api.PaymentsClient({
          environment: options.debug ? 'TEST' : 'PRODUCTION',
          paymentDataCallbacks: {
            onPaymentAuthorized: methods.googlePayPaymentAuthed
          }
        });
        paypal.Googlepay().config().then(config => {
          googlePayConfig = config;
          googlePayClient.isReadyToPay({
            apiVersion: googlePayConfig.apiVersion,
            apiVersionMinor: googlePayConfig.apiVersionMinor,
            allowedPaymentMethods: googlePayConfig.allowedPaymentMethods
          }).then(function (response) {
            if (!response.result) {
              return;
            }
            const button = googlePayClient.createButton({
              buttonType: 'pay',
              buttonSizeMode: 'fill',
              allowedPaymentMethods: googlePayConfig.allowedPaymentMethods,
              onClick: methods.googlePayHandleClicked
            });
            $('#' + submitButtonId + '_googlepay-container').html(button);
          }).catch(error => console.error(error));
        }).catch(error => console.error(error));
      }).fail(error => console.error(error));
    },
    /**
     * Gets PayPal Smart Buttons script load options.
     *
     * @param $payment
     * @returns {{clientId: string, debug: boolean, disableFunding: string, currency: string}}
     */
    getSmartButtonsLoadOptions: function ($payment) {
      return {
        clientId: $payment.data('caPaypalCheckoutClientId'),
        currency: $payment.data('caPaypalCheckoutCurrency'),
        company_name: $payment.data('caPaypalCheckoutCompanyName'),
        disableFunding: $payment.data('caPaypalCheckoutDisableFunding'),
        debug: $payment.data('caPaypalCheckoutDebug')
      };
    },
    /**
     * Gets URL to load the customized PayPal Smart Buttons script.
     * @param {object} options
     * @returns {string}
     */
    getSmartButtonsLoadUrl: function (options) {
      var url = 'https://www.paypal.com/sdk/js' + '?client-id=' + options.clientId + '&components=buttons,applepay,googlepay' + '&currency=' + options.currency + '&debug=' + (options.debug ? 'true' : 'false') + '&intent=capture' + '&commit=true' + '&integration-date=2022-10-10';
      if (options.disableFunding) {
        url += '&disable-funding=' + options.disableFunding;
      }
      return url;
    },
    applePayCheck: () => {
      return new Promise((resolve, reject) => {
        let errorMsg = '';
        if (!window.ApplePaySession) {
          errorMsg = 'This device does not support Apple Pay';
        } else if (!ApplePaySession.canMakePayments()) {
          errorMsg = 'This device, although an Apple device, is not capable of making Apple Pay payments';
        }
        if (errorMsg !== "") {
          reject(errorMsg);
        } else {
          resolve();
        }
      });
    },
    applePayPaymentAuthed: event => {
      applePayPaymentEvent = event.payment;
      orderId = null;
      var deferredOrder = $.Deferred(),
        billingAddress = methods.getBillingAddress(applePayPaymentEvent.billingContact);
      methods.placeOrder(deferredOrder, paymentForm, billingAddress);
      deferredOrder.promise().then(function (success) {
        orderId = success.order_id;
        applePay.confirmOrder({
          orderId: success.order_id_in_paypal,
          token: applePayPaymentEvent.token,
          billingContact: applePayPaymentEvent.billingContact
        }).then(confirmResult => {
          applePaySession.completePayment(ApplePaySession.STATUS_SUCCESS);
          methods.redirect(orderId, success.order_id_in_paypal);
        }).catch(confirmError => {
          if (confirmError) {
            console.error('Error confirming order with applepay token');
            console.error(confirmError);
            applePaySession.completePayment(ApplePaySession.STATUS_FAILURE);
          }
        });
      }, function (fail) {
        new Error(fail.error);
      });
    },
    applePayValidate: event => {
      applePay.validateMerchant({
        validationUrl: event.validationURL,
        displayName: options.company_name
      }).then(validateResult => {
        applePaySession.completeMerchantValidation(validateResult.merchantSession);
      }).catch(validateError => {
        console.error(validateError);
        applePaySession.abort();
      });
    },
    applePayHandleClicked: event => {
      if (!paymentForm.ceFormValidator('checkFields', false)) {
        return;
      }
      const payment_request = {
        countryCode: applePayConfig.countryCode,
        merchantCapabilities: applePayConfig.merchantCapabilities,
        supportedNetworks: applePayConfig.supportedNetworks,
        currencyCode: applePayConfig.currencyCode,
        requiredShippingContactFields: [],
        requiredBillingContactFields: ['postalAddress'],
        total: {
          label: options.company_name,
          type: 'final',
          amount: orderTotal
        }
      };
      applePaySession = new ApplePaySession(applePayVersion, payment_request);
      applePaySession.onvalidatemerchant = methods.applePayValidate;
      applePaySession.onpaymentauthorized = methods.applePayPaymentAuthed;
      applePaySession.begin();
    },
    googlePayPaymentAuthed: paymentData => {
      orderId = null;
      var deferredOrder = $.Deferred(),
        billingAddress = methods.getBillingAddress(paymentData.paymentMethodData.info.billingAddress);
      methods.placeOrder(deferredOrder, paymentForm, billingAddress);
      return new Promise(function (resolve) {
        deferredOrder.promise().then(success => {
          orderId = success.order_id;
          paypal.Googlepay().confirmOrder({
            orderId: success.order_id_in_paypal,
            paymentMethodData: paymentData.paymentMethodData
          }).then(confirmResult => {
            if (confirmResult.status === 'PAYER_ACTION_REQUIRED') {
              paypal.Googlepay().initiatePayerAction({
                orderId: success.order_id_in_paypal
              }).then(() => {
                resolve({
                  transactionState: 'SUCCESS'
                });
                methods.redirect(orderId, success.order_id_in_paypal);
              }).catch(error => console.error(error));
            } else {
              resolve({
                transactionState: 'SUCCESS'
              });
              methods.redirect(orderId, success.order_id_in_paypal);
            }
          }).catch(error => {
            if (error) {
              console.error('Error confirming order with google pay');
              console.error(error);
            }
            resolve({
              transactionState: 'ERROR'
            });
          });
        }, fail => {
          resolve({
            transactionState: 'ERROR'
          });
          new Error(fail.error);
        });
      });
    },
    googlePayHandleClicked: () => {
      if (!paymentForm.ceFormValidator('checkFields', false)) {
        return;
      }
      const paymentDataRequest = {
        apiVersion: googlePayConfig.apiVersion,
        apiVersionMinor: googlePayConfig.apiVersionMinor,
        allowedPaymentMethods: googlePayConfig.allowedPaymentMethods,
        merchantInfo: googlePayConfig.merchantInfo,
        callbackIntents: ['PAYMENT_AUTHORIZATION'],
        transactionInfo: {
          countryCode: googlePayConfig.countryCode,
          currencyCode: options.currency,
          totalPriceStatus: 'FINAL',
          totalPrice: orderTotal
        }
      };
      googlePayClient.loadPaymentData(paymentDataRequest);
    },
    redirect: (orderId, paypalOrderId) => {
      var redirectUrl = fn_url('payment_notification.return' + '?order_id=' + orderId + '&order_id_in_paypal=' + paypalOrderId + '&payment=paypal_checkout');
      $.redirect(redirectUrl);
    },
    placeOrder: (promise, paymentForm, billingAddress) => {
      var orderRequest = methods.getOrderPlacementRequest(paymentForm),
        dispatch = 'checkout.place_order';
      if (isRepayOrder) {
        dispatch = 'orders.repay';
      }
      orderRequest.user_data = {
        ...(orderRequest === null || orderRequest === void 0 ? void 0 : orderRequest.user_data),
        ...billingAddress
      };
      $.ceAjax('request', fn_url(dispatch), {
        data: orderRequest,
        method: 'post',
        hidden: true,
        caching: false,
        callback: function (res) {
          if (res.error) {
            promise.reject(res);
            return;
          }
          if (res.order_id_in_paypal) {
            promise.resolve(res);
            return;
          }
          promise.reject({
            error: ''
          });
        }
      });
    },
    getBillingAddress: rawBillingAddress => {
      var billingAddress = {},
        value,
        fieldsMap = {
          givenName: 'b_firstname',
          familyName: 'b_lastname',
          countryCode: 'b_country',
          administrativeArea: 'b_state',
          locality: 'b_city',
          postalCode: 'b_zipcode',
          addressLines: 'b_address',
          name: 'b_firstname',
          address1: 'b_address',
          address2: 'b_address',
          address3: 'b_address'
        };
      for (var field in fieldsMap) {
        value = '';
        if (!Object.hasOwn(rawBillingAddress, field)) {
          continue;
        }
        value = rawBillingAddress[field];
        if (Array.isArray(value)) {
          value = value.join(' ');
        }
        if (value && Object.hasOwn(billingAddress, fieldsMap[field])) {
          value = billingAddress[fieldsMap[field]] + ' ' + value;
          billingAddress[fieldsMap[field]] = value;
        } else if (value) {
          billingAddress[fieldsMap[field]] = value;
        }
      }
      return billingAddress;
    },
    /**
     * Initializes payment form.
     *
     * @param {jQuery} $payment Payment method
     */
    init: function ($payment) {
      var $payment_form = $payment.closest('form');
      submitButtonId = methods.setSubmitButtonId($payment.data('caPaypalCheckoutButton'));
      var $submitButton = $('#' + submitButtonId);
      $submitButton.addClass('hidden');
      var checkoutScriptLoadCallback = function () {
        isCheckoutScriptLoaded = true;
        methods.setupWindowClosedErrorHandler(window);
        methods.setupPaymentForm({
          payment_form: $payment_form,
          submit_button_id: submitButtonId,
          style: {
            layout: $payment.data('caPaypalCheckoutStyleLayout'),
            color: $payment.data('caPaypalCheckoutStyleColor'),
            height: $payment.data('caPaypalCheckoutStyleHeight'),
            shape: $payment.data('caPaypalCheckoutStyleShape'),
            label: $payment.data('caPaypalCheckoutStyleLabel'),
            tagline: $payment.data('caPaypalCheckoutStyleTagline')
          }
        });
      };
      if (isCheckoutScriptLoaded) {
        checkoutScriptLoadCallback();
      } else {
        options = methods.getSmartButtonsLoadOptions($payment);
        var url = methods.getSmartButtonsLoadUrl(options);
        methods.loadApplePayScript();
        methods.loadPayPalScript(url, checkoutScriptLoadCallback);
        googlePayDeffered = methods.loadGooglePayScript();
      }
    },
    /**
     * Forbids order placement (e.g., due to the validation)
     *
     * @param {object} actions
     */
    forbidOrderPlacement(actions) {
      isPlaceOrderAllowed = false;
      actions.disable();
    },
    /**
     * Allows order placement.
     *
     * @param {object} actions
     */
    allowOrderPlacement(actions) {
      isPlaceOrderAllowed = true;
      actions.enable();
    },
    /**
     * Runs validation loop on the order placement fom.
     *
     * @param {jQuery} $paymentForm
     * @param {object} actions
     */
    startValidation($paymentForm, actions) {
      validationLoop = setInterval(function () {
        var formIsValid = $paymentForm.ceFormValidator('checkFields', true);
        if (formIsValid && !isPlaceOrderAllowed) {
          methods.allowOrderPlacement(actions);
        } else if (!formIsValid && isPlaceOrderAllowed) {
          methods.forbidOrderPlacement(actions);
        }
      }, 300);
    },
    /**
     * Stops validation on the order placement form.
     */
    stopValidation() {
      if (validationLoop) {
        clearInterval(validationLoop);
      }
    },
    /**
     * Creates container for PayPal Smart Buttons.
     *
     * @param {string} submitButtonId
     */
    createPaymentButtonsContainer(submitButtonId) {
      $('<div class="ty-paypal-checkout-buttons-container" id="' + submitButtonId + '_container">' + '<div id="' + submitButtonId + '_applepay-container"></div>' + '<div id="' + submitButtonId + '_googlepay-container" class="ty-paypal-checkout-google-pay-button"></div></div>').insertAfter($('#' + submitButtonId));
    },
    /**
     * Sets up global error handler to work around the following issue:
     * https://github.com/paypal/paypal-checkout-components/issues/1107.
     *
     * @param {window} window
     */
    setupWindowClosedErrorHandler(window) {
      // Window closed
      window.onerror = function (message, source, lineno, colno, error) {
        console.log(message, source, lineno, colno, error);
      };
    },
    /**
     * Loads Smart Payment Buttons script.
     *
     * @param {string}   url                        Script URL
     * @param {callback} checkoutScriptLoadCallback Action to execute after script is loaded
     */
    loadPayPalScript(url, checkoutScriptLoadCallback) {
      var checkoutScript = _.doc.createElement('script');
      checkoutScript.setAttribute('src', url);
      checkoutScript.setAttribute('data-partner-attribution-id', 'CSCART_PPC');
      checkoutScript.onload = checkoutScriptLoadCallback;
      _.doc.head.appendChild(checkoutScript);
    },
    loadApplePayScript() {
      var checkoutScript = _.doc.createElement('script');
      checkoutScript.setAttribute('src', 'https://applepay.cdn-apple.com/jsapi/v1/apple-pay-sdk.js');
      _.doc.head.appendChild(checkoutScript);
    },
    loadGooglePayScript() {
      var checkoutScript = _.doc.createElement('script'),
        deffered = $.Deferred();
      checkoutScript.setAttribute('src', 'https://pay.google.com/gp/p/js/pay.js');
      checkoutScript.onload = () => {
        deffered.resolve();
      };
      checkoutScript.onerror = () => {
        deffered.reject('Error on load the Google Pay script');
      };
      _.doc.head.appendChild(checkoutScript);
      return deffered;
    },
    setup: context => {
      if (_.embedded) {
        return;
      }
      var isCheckoutButtonLoaded = !!$('[name="dispatch[checkout.place_order]"]', context).length,
        $payment = $('[data-ca-paypal-checkout]');
      isRepayOrder = !!$('[name="dispatch[orders.repay]"]', context).length;
      if ($payment.length) {
        orderTotal = String($payment.data('caPaypalCheckoutTotal'));
      }
      if (!isCheckoutButtonLoaded && !isRepayOrder) {
        return;
      }
      if (!$payment.length) {
        return;
      }
      methods.init($payment);
    }
  };
  $.extend({
    cePaypalCheckout: function (method) {
      if (methods[method]) {
        return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
      } else {
        $.error('ty.paypalCheckout: method ' + method + ' does not exist');
      }
    }
  });
  $.ceEvent('on', 'ce.commoninit', function (context) {
    $.cePaypalCheckout('setup', context);
  });
  $.cePaypalCheckout('setup', document);
})(Tygh, Tygh.$);
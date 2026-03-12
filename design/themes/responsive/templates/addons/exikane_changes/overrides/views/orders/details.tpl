{if $order_info}
    {$order_statuses = $smarty.const.STATUSES_ORDER|fn_get_simple_statuses:true}
    {$booking_product_key = ""}
    {assign var="booking_product" value=array()}
    {assign var="booking_status" value=array()}
    {$booking_statuses = fn_ec_table_booking_system_get_booking_status_params()}

    {foreach $order_info.products as $product_key => $product}
        {$booking_product_key = $product_key}
        {$booking_product = $product}
        {break}
    {/foreach}

    {if $booking_product.product_id}
        {$booking_status = fn_ec_table_booking_system_check_booking_order_status($order_info.order_id, $booking_product.product_id)}
    {/if}

    {$booking_status_code = $booking_status.status|default:""}
    {$booking_status_title = ""}
    {if $booking_status_code && $booking_statuses[$booking_status_code]}
        {$booking_status_title = $booking_statuses[$booking_status_code]}
    {else}
        {$booking_status_title = $order_statuses[$order_info.status]|default:$order_info.status}
        {if $booking_status_title|@is_array && $booking_status_title.description}
            {$booking_status_title = $booking_status_title.description}
        {/if}
    {/if}

    {assign var="booking_info" value=$order_info.exikane_booking_info|default:array()}

    {capture name="mainbox_title"}{/capture}

    <div class="ty-booking-details">
        <section class="ty-booking-details__hero">
            <div class="ty-booking-details__hero-main">
                <h1 class="ty-booking-details__title">{$order_info.exikane_booking_product_name}</h1>

                {if $order_info.exikane_booking_type}
                    <div class="ty-booking-details__type-badge">{$order_info.exikane_booking_type}</div>
                {/if}

                <div class="ty-booking-details__meta">
                    {if $booking_info.booking_type == "T" && $booking_info.booking_date}
                        <div class="ty-booking-details__meta-item">
                            <i class="ut2-icon-calendar ty-booking-details__meta-icon"></i>
                            <span>
                                {$booking_info.booking_date|date_format:"%e %B %Y, %A"}
                                {if $order_info.exikane_booking_slot_start}
                                    {__("exikane_changes.booking_time_prefix")} {$order_info.exikane_booking_slot_start}
                                {/if}
                            </span>
                        </div>
                    {elseif $booking_info.from || $booking_info.to}
                        <div class="ty-booking-details__meta-item">
                            <i class="ut2-icon-calendar ty-booking-details__meta-icon"></i>
                            <span>{$booking_info.from}{if $booking_info.to} - {$booking_info.to}{/if}</span>
                        </div>
                    {/if}

                    {if $order_info.exikane_booking_address}
                        <div class="ty-booking-details__meta-item">
                            <i class="ut2-icon-location_on ty-booking-details__meta-icon"></i>
                            <span style="color: #d29815">{$order_info.exikane_booking_address}</span>
                        </div>
                    {/if}

                    {if $order_info.notes}
                        <div class="ty-booking-details__meta-item">
                            <i class="ut2-icon-article ty-booking-details__meta-icon"></i>
                                <span style="color: #d29815">{$order_info.notes}</span>
                        </div>
                    {/if}
                    <p class="ty-muted">{__("exikane_changes.booking_number")}: #{$order_info.order_id}</p>
                </div>
            </div>

            <div class="ty-booking-details__hero-status">
                <span class="ty-booking-details__status-pill ty-booking-details__status-pill--{$booking_status_code|default:$order_info.status|lower}">
                    {$booking_status_title}
                </span>
            </div>
        </section>

        <div class="ty-booking-details__grid{if !$qr_code_url} ty-booking-details__grid--single{/if}">
            <div class="ty-booking-details__content">
                <section class="ty-booking-details__card">
                    <h2 class="ty-booking-details__card-title">{__("exikane_changes.lesson_information")}</h2>

                    <div class="ty-booking-details__lesson">
                        <div class="ty-booking-details__lesson-image">
                            {include
                                file="common/image.tpl"
                                images=$booking_product.main_pair
                                image_width=120
                                image_height=120
                                obj_id=$booking_product_key
                            }
                        </div>

                        <div class="ty-booking-details__lesson-main">
                            <div class="ty-booking-details__lesson-head">
                                <div class="ty-booking-details__lesson-summary">
                                    <div class="ty-booking-details__lesson-name">{$order_info.exikane_booking_product_name}</div>
                                    {if $booking_product.product_url}
                                        <a class="ty-booking-details__lesson-link ty-btn ty-btn__secondary" href="{$booking_product.product_url}">
                                            {__("exikane_changes.open_lesson_card")}
                                        </a>
                                    {/if}
                                </div>
                            </div>

                            <div class="ty-booking-details__lesson-facts">
                                {if $order_info.exikane_booking_address}
                                    <div class="ty-booking-details__fact">
                                        <div class="ty-booking-details__fact-label">{__("exikane_changes.booking_address")}:</div>
                                        <div class="ty-booking-details__fact-value">{$order_info.exikane_booking_address}</div>
                                    </div>
                                {/if}

                                {if $booking_info.booking_type == "T" && $booking_info.booking_date}
                                    <div class="ty-booking-details__fact">
                                        <div class="ty-booking-details__fact-label">{__("exikane_changes.booking_date_time")}:</div>
                                        <div class="ty-booking-details__fact-value">
                                            {$booking_info.booking_date|date_format:"%e %B %Y"}
                                            {if $order_info.exikane_booking_slot_start}, {$order_info.exikane_booking_slot_start}{/if}
                                        </div>
                                    </div>
                                {elseif $booking_info.from || $booking_info.to}
                                    <div class="ty-booking-details__fact">
                                        <div class="ty-booking-details__fact-label">{__("exikane_changes.booking_date_time")}:</div>
                                        <div class="ty-booking-details__fact-value">{$booking_info.from}{if $booking_info.to} - {$booking_info.to}{/if}</div>
                                    </div>
                                {/if}

                                {if $order_info.exikane_booking_type}
                                    <div class="ty-booking-details__fact">
                                        <div class="ty-booking-details__fact-label">{__("exikane_changes.lesson_format")}:</div>
                                        <div class="ty-booking-details__fact-value">{$order_info.exikane_booking_type}</div>
                                    </div>
                                {/if}

                                {if $order_info.exikane_booking_age}
                                    <div class="ty-booking-details__fact">
                                        <div class="ty-booking-details__fact-label">{__("exikane_changes.booking_age")}:</div>
                                        <div class="ty-booking-details__fact-value">{$order_info.exikane_booking_age}</div>
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </section>

                <section class="ty-booking-details__card">
                    <h2 class="ty-booking-details__card-title">{__("exikane_changes.payment_block_title")}</h2>

                    <div class="ty-booking-details__payment">
                        <div class="ty-booking-details__payment-row">
                            <span>{__("exikane_changes.booking_cost")}:</span>
                            <strong>{include file="common/price.tpl" value=$order_info.exikane_products_total}</strong>
                        </div>

                        <div class="ty-booking-details__payment-row">
                            <span>{__("exikane_changes.points_spent")}:</span>
                            <strong class="ty-booking-details__payment-accent">
                                {if $order_info.exikane_points_cost|floatval > 0}-{/if}{include file="common/price.tpl" value=$order_info.exikane_points_cost}
                            </strong>
                        </div>

                        <div class="ty-booking-details__payment-total">
                            <span><strong>{__("exikane_changes.total_amount")}:</strong></span>
                            <strong>{include file="common/price.tpl" value=$order_info.exikane_paid_total}</strong>
                        </div>
                    </div>
                </section>

                <section class="ty-booking-details__card">
                    <h2 class="ty-booking-details__card-title">{__("exikane_changes.actions_title")}</h2>

                    {if $addons.vendor_communication.show_on_order === "YesNo::YES"|enum && !$vendor_communication_order_thread}
                        <div class="ty-booking-details__actions">
                            {include
                                file="addons/vendor_communication/views/vendor_communication/components/new_thread_button.tpl"
                                title=__("exikane_changes.contact_support_to_cancel")
                                object_id=$order_info.order_id
                                meta="ty-booking-details__support-btn ty-btn ty-btn__primary"
                                show_form=false
                            }
                        </div>
                    {/if}

                    <div class="ty-booking-details__actions-note">
                        {__("exikane_changes.cancellation_terms_prefix")}
                        <span class="ty-booking-details__actions-note-accent">{__("exikane_changes.cancellation_terms_highlight")}</span>.
                    </div>
                </section>
            </div>

            {if $qr_code_url}
                <aside class="ty-booking-details__sidebar">
                    <section class="ty-booking-details__qr-card">
                        <h2 class="ty-booking-details__qr-title">{__("exikane_changes.qr_code_title")}</h2>

                        <div class="ty-booking-details__qr-box">
                            <img src="{$qr_code_url}" alt="{__("exikane_changes.qr_code_alt")}" />
                        </div>

                        <div class="ty-booking-details__qr-note">
                            <i class="ut2-icon-outline-info-circle ty-booking-details__qr-note-icon"></i>
                            {__("exikane_changes.qr_code_note")}
                        </div>

                        <a class="ty-booking-details__qr-download ty-btn ty-btn__primary" href="{$qr_code_url}" download="booking-qr-{$order_info.order_id}.png">
                            {__("exikane_changes.download_qr_code")}
                        </a>

                        {if $order_info.exikane_calendar_event_available}
                            <a class="ty-booking-details__calendar-link ty-btn ty-btn__secondary" href="{"exikane_changes.calendar_event?order_id=`$order_info.order_id`"|fn_url}">
                                <i class="ut2-icon-addchart"></i>
                                {__("exikane_changes.add_to_calendar")}
                            </a>
                        {/if}
                    </section>
                </aside>
            {/if}
        </div>
    </div>

    {if $addons.vendor_communication.show_on_order === "YesNo::YES"|enum && !$vendor_communication_order_thread}
        {if $auth.user_id}
            {if "MULTIVENDOR"|fn_allowed_for}
                {$vendor_name = $order_info.company_id|fn_get_company_name:"":$smarty.const.CART_LANGUAGE:["use_i18n_fields" => true]}
            {/if}
            {include
                file="addons/vendor_communication/views/vendor_communication/components/new_thread_form.tpl"
                object_type=$smarty.const.VC_OBJECT_TYPE_ORDER
                object_id=$order_info.order_id
                company_id=$order_info.company_id
                vendor_name=$vendor_name|default:""
                redirect_url="`$config.current_url`&selected_section=vendor_communication"
                no_ajax=true
                product=false
            }
        {else}
            {include file="addons/vendor_communication/views/vendor_communication/components/login_form.tpl"}
        {/if}
    {/if}
{/if}

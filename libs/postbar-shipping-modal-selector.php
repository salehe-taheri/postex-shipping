<?php

function postbar_shipping_modal_selector() { 

    // ajax and nonces
    $shippingModalAjaxUrl = admin_url('admin-ajax.php');
    $nonce_ajaxCheckConditionalShipping = wp_create_nonce( "nonce-ajaxCheckConditionalShipping" );
    $nonce_ajaxPostbarSetSessions = wp_create_nonce( "nonce-ajaxPostbarSetSessions" );
    $nonce_ajaxPostbarStateTownsHTML = wp_create_nonce( "nonce-ajaxPostbarStateTownsHTML" );
    $nonce_ajaxPostbarServicesHTML = wp_create_nonce( "nonce-ajaxPostbarServicesHTML" ); 

    // set default sessions
    if(!WC()->session->get( 'postbar_reciever_stateId', 0 ))
    {
        WC()->session->set("postbar_reciever_stateId", get_option('postbar_woo_shipping_opts')["reciver_default_stateId"] ); 
    }
    if(!WC()->session->get( 'postbar_reciever_townId', 0 ))   
    {
        WC()->session->set("postbar_reciever_townId", get_option('postbar_woo_shipping_opts')["reciver_default_townId"] );
    }        

    //state and town
    $postbar_reciever_stateId = WC()->session->get( 'postbar_reciever_stateId', 0 ) ? WC()->session->get( 'postbar_reciever_stateId', 0 ) : get_option('postbar_woo_shipping_opts')["reciver_default_stateId"];
    $postbar_reciever_townId = WC()->session->get( 'postbar_reciever_townId', 0 ) ? WC()->session->get( 'postbar_reciever_townId', 0 ) : get_option('postbar_woo_shipping_opts')["reciver_default_townId"];
    $states_options = postbarStatesHTML($postbar_reciever_stateId);
    $towns_options = postbarStateTownsHTML($postbar_reciever_townId , $postbar_reciever_stateId);
        
    $shippingPriceCalcType = get_option('postbar_woo_shipping_opts')["shippingPriceCalcType"];
    if( $shippingPriceCalcType == "1" )
    {
        // payment methods
        $enabled_payments = enabled_payments();
        if( $enabled_payments->bothEnabled )
        {
            $is_COD = WC()->session->get( 'postbar_IsCOD', 0 ) ? WC()->session->get( 'postbar_IsCOD', 0 ) : 'false';
        }
        else
        {
            $is_COD = $enabled_payments->singleEnabled == 'cod' ? 'true' : 'false';
        }

        // listType        
        $postbar_reciever_listType = (get_option('postbar_woo_shipping_opts')["ListTypeSelectable"] && WC()->session->get( 'postbar_reciever_listType', 0 )) ? WC()->session->get( 'postbar_reciever_listType', 0 ) : get_option('postbar_woo_shipping_opts')["ListType"];

        // shipping Service
        $postbar_UserSelected_ServiceId = WC()->session->get( 'postbar_UserSelected_ServiceId', 0 ) ? WC()->session->get( 'postbar_UserSelected_ServiceId', 0 ) : '';
        $services_items = postbarServicesHTML($postbar_reciever_stateId, $postbar_reciever_townId, $postbar_reciever_listType, $postbar_UserSelected_ServiceId, $is_COD);
        
        // ListType filter
        $ListTypeSelectable = get_option('postbar_woo_shipping_opts')["ListTypeSelectable"];
        if($ListTypeSelectable)
        {
            $ListType = WC()->session->get( 'postbar_reciever_listType', 0 ) ? WC()->session->get( 'postbar_reciever_listType', 0 ) : get_option('postbar_woo_shipping_opts')["ListType"];
            $all_selected = "";
            $fastest_selected = "";
            $cheapest_selected = "";
            if($ListType=="1")
                $fastest_selected = "selected";
            if($ListType=="2")
                $cheapest_selected = "selected";
            $listTypes_html = "
                <div id='postbar_reciever_listType_container' class='postbar-act-form-control'>
                    <label for='postbar_reciever_listType'>?????????? ?????????? ????</label>                         
                    <select id='postbar_reciever_listType'>
                        <option value='0' $all_selected >?????? ?????????? ????</option>
                        <option value='1' $fastest_selected >???????? ????????</option>
                        <option value='2' $cheapest_selected >?????????? ????????</option>
                    </select>
                </div>
            ";
        }
        else
        {
            $listTypes_html = "";
        }

        // cod filter
        if( $enabled_payments->bothEnabled )
        {
            $cod_selected = (WC()->session->get( 'postbar_IsCOD', 0 ) && WC()->session->get( 'postbar_IsCOD', 0 )=='true') ? 'selected' : '';
            $online_selected = $cod_selected == 'selected' ? '' : 'selected';

            $isCOD_html = "
                <div id='postbar_IsCOD_container' class='postbar-act-form-control'>
                    <label for='postbar_IsCOD'>?????? ????????????</label>                         
                    <select id='postbar_IsCOD'>
                        <option value='false' $online_selected >???????????? ???????????? (?????? ??????????)</option>
                        <option value='true' $cod_selected >???????????? ???? ?????? (???? ??????????)</option>
                    </select>
                </div>
            ";
        }
        else
        {
            $isCOD_html = "";
        }

        $PACT_html = "
        <div id='postexShippingModalSelector_container'>
            <div id='postex-shipping-modal-selector'>
                <input type='hidden' id='postbarShippingModalAjaxUrl' value='$shippingModalAjaxUrl' />
                <input type='hidden' id='nonce_ajaxCheckConditionalShipping' value='$nonce_ajaxCheckConditionalShipping' />
                <input type='hidden' id='nonce_ajaxPostbarSetSessions' value='$nonce_ajaxPostbarSetSessions' />
                <input type='hidden' id='nonce_ajaxPostbarStateTownsHTML' value='$nonce_ajaxPostbarStateTownsHTML' />
                <input type='hidden' id='nonce_ajaxPostbarServicesHTML' value='$nonce_ajaxPostbarServicesHTML' />
                <div id='postbar_services_header'>
                    <div id='postex-close-modal-icon'></div>
                    <h3>???????????? ?????????? ?????? ?? ??????</h3>
                </div>
                <div id='postbar_services_content' class='shipping-info-content'>
                    <p>
                        <b>???????? : </b>
                        ?????????? ?????????? ?????????? ???????????? ???? ?????? ???????????? ?????? ????????????. ???????? ?????? ???????????? ???????? ???? ?????????? ???????? ?? ?????? ???? ?????? ?????????? ?????? ?????????? ???? ?????????? ???? ???????????? ????????.
                    </p>
                    <div class='form-items'>
                        <div class='postbar-act-form-control'>
                            <label for='postbar_reciever_stateId'>??????????</label>
                            <select id='postbar_reciever_stateId'>
                                $states_options
                            </select>
                        </div>
                        <div class='postbar-act-form-control'>
                            <label for='postbar_reciever_townId'>??????????????</label>                         
                            <select id='postbar_reciever_townId'>
                                $towns_options
                            </select>
                        </div> 
                        $listTypes_html 
                        $isCOD_html
                    </div>
                    <div id='postex_shipping_services_container' class='shipping-services-types-table'>
                        <div class='title-label'>?????????? ?????? ???????? ??????????</div>
                        <div class='shipping-row titles'>
                            <div class='shipping-selection'>????????????</div>
                            <div class='shipping-details'>
                                <div class='details-item'>?????? ??????????</div>
                                <div class='details-item'>?????? ???????? ?????????? (??????)</div>
                                <div class='details-item'>???????? (????????)</div>
                            </div>                
                        </div>
                        <div id='shipping_rows_container'>$services_items</div>                
                    </div>
                </div>       
                <div class='postex-modal-footer'>
                    <button id='postex_modal_btn_cancel'>????????????</button>
                    <button id='postex_modal_btn_save' class='postex-btn-has-loading'>
                        <span class='postex-btn-loading'></span>
                        <span class='postex-btn-txt'>??????????</span>
                    </button>
                </div> 
            </div>
        </div>";
    } 
    else
    {
        $PACT_html = "
        <div id='postexShippingModalSelector_container'>
            <div id='postex-shipping-modal-selector'>
                <input type='hidden' id='postbarShippingModalAjaxUrl' value='$shippingModalAjaxUrl' />
                <input type='hidden' id='nonce_ajaxPostbarSetSessions' value='$nonce_ajaxPostbarSetSessions' />
                <input type='hidden' id='nonce_ajaxPostbarStateTownsHTML' value='$nonce_ajaxPostbarStateTownsHTML' />
                <div id='postbar_services_header'>
                    <div id='postex-close-modal-icon'></div>
                    <h3>???????????? ???????? ?????? ?? ??????</h3>
                </div>
                <div id='postbar_services_content' class='shipping-info-content'>
                    <p>
                        <b>???????? : </b>
                        ???????? ?????? ???????????? ???????? ???? ?????????? ????????.
                    </p>
                    <div class='form-items'>
                        <div class='postbar-act-form-control'>
                            <label for='postbar_reciever_stateId'>??????????</label>
                            <select id='postbar_reciever_stateId'>
                                $states_options
                            </select>
                        </div>
                        <div class='postbar-act-form-control'>
                            <label for='postbar_reciever_townId'>??????????????</label>                         
                            <select id='postbar_reciever_townId'>
                                $towns_options
                            </select>
                        </div>
                    </div>
                </div>       
                <div class='postex-modal-footer'>
                    <button id='postex_modal_btn_cancel'>????????????</button>
                    <button id='postex_modal_btn_save' class='postex-btn-has-loading'>
                        <span class='postex-btn-loading'></span>
                        <span class='postex-btn-txt'>??????????</span>
                    </button>
                </div> 
            </div>
        </div>";
    } // End if shippingPriceCalcType == 1

    echo $PACT_html;        
    
};
         
// add the action 
add_action( 'woocommerce_before_cart', 'postbar_shipping_modal_selector' ); 
add_action( 'woocommerce_before_checkout_form', 'postbar_shipping_modal_selector' );
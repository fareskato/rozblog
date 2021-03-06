<div class="col-lg-12">
    <form action="" method="post" id="address_form">


        <?php if (! empty( $otherAddr ) ): ?>

            <div id="addresses">

                <?php foreach ( $otherAddr as $idx => $address ): ?>
                    <div class="shipping_address address_block col-lg-6 multiple-shipping-form" id="shipping_address_<?php echo $idx; ?>">
                        <div class="">


                        <?php
                        do_action( 'woocommerce_before_checkout_shipping_form', $checkout);

                        foreach ( $shipFields as $key => $field ) {
                            $val = '';

                            if ( isset( $address[ $key ] ) ) {
                                $val = $address[$key];
                            }

                            $key .= '[]';

                            woocommerce_form_field( $key, $field, $val );
                        }

                        do_action( 'woocommerce_after_checkout_shipping_form', $checkout);
                        ?>
                            <p align="right"><a href="#" class="button delete"><?php _e('delete', 'wc_shipping_multiple_address'); ?></a></p>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div id="addresses">

                <?php
                foreach ( $shipFields as $key => $field ) :
                    $key .= '[]';
                    $val = '';

                    woocommerce_form_field( $key, $field, $val );
                endforeach;
                ?>
            </div>
        <?php endif; ?>

    <div class="col-lg-12">
        <div class="col-lg-6">
            <p></p>
        </div>
        <div class="col-lg-6">
            <div class="form-row right">
                <a class="button add_address" href="#"><?php _e( 'Add another', 'wc_shipping_multiple_address' ); ?></a>
                <input type="hidden" name="shipping_account_address_action" value="save" />
                <input type="submit" name="set_addresses" value="<?php _e( 'Save Addresses', 'wc_shipping_multiple_address' ); ?>" class="pink-button" />
            </div>
        </div>

    </div>

    </form>
</div>

<script type="text/javascript">
    var tmpl = '<div class="shipping_address address_block col-lg-6 multiple-shipping-form"><div class="">';

    tmpl += '\
            <?php
            foreach ($shipFields as $key => $field) :
                $key .= '[]';
                $val = '';
                $field['return'] = true;
                $row = woocommerce_form_field( $key, $field, $val );
                echo str_replace("\n", "\\\n", $row);
            endforeach;
            ?>
    ';

    tmpl += '<p align="right"><a href="#" class="button delete"><?php _e('delete', 'wc_shipping_multiple_address'); ?></a></p></div></div>';
    jQuery(".add_address").click(function(e) {
        e.preventDefault();

        jQuery("#addresses").append(tmpl);
    });

    jQuery(".delete").on("click", function(e) {
        e.preventDefault();
        jQuery(this).parents("div.address_block").remove();
    });

    jQuery(document).ready(function() {
        jQuery("#address_form").submit(function() {
            var valid = true;
            jQuery("input[type=text],select, input[type='hidden']").each(function() {
                if (jQuery(this).prev("label").children("abbr").length == 1 && jQuery(this).val() == "") {
                    jQuery(this).focus();
                    valid = false;
                    return false;
                }
            });
            return valid;
        });
    });
</script>
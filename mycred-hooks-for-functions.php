// experimental code for functions.php

// myCred hooks
// Ref: https://mycred.me/tutorials/how-to-make-your-custom-hook/
add_action( 'mycred_setup_hooks', 'mycred_register_earlybird_hook' );
function mycred_register_earlybird_hook( $installed )
{
    $installed['earlybird'] = array(
        'title'       => __( 'points for Early Bird submission', 'textdomain' ),
        'description' => __( 'Award points for submissions in the first half of the monthly submission period', 'textdomain' ),
        'callback'    => array( 'mycred_earlybird_hook_class' )
    );
    return $installed;
}
//
add_action( 'mycred_load_hooks', 'mycred_load_earlybird_hook', 10 );
function mycred_load_earlybird_hook() {
    debug("Early Bird action called");
    // The myCred_Hook class is an abstract class used by all myCred Hooks. It contains 
    // commonly used methods to help you build hooks without to much programming
    class mycred_earlybird_hook_class extends myCRED_Hook {
        //
        // Class constructor. Will set the hooks id and apply default settings if not set.
        function __construct( $hook_prefs, $type = 'mycred_default' ) {
            debug("Early Bird class instantiated");
            parent::__construct( array(
                'id'       => 'earlybird',
                'defaults' => array(
                    'post_type' => 'thing',
                    'creds'   => 20,
                    'log'     => '%plural% for Early Bird submission'
                )
            ), $hook_prefs, $type );
        }
        //
        // This method is called by myCred and will hold our actions / filters.
        // hook into wp
        public function run() {
            // Since we are running a single instance, we do not need to check
            // if points are set to zero (disable). myCRED will check if this
            // hook has been enabled before calling this method so no need to check
            // that either.
            // these examples are triggered by these wp actions and call the method below 
            // add_action( 'personal_options_update',  array( $this, 'profile_update' ) );
            // add_action( 'edit_user_profile_update', array( $this, 'profile_update' ) );
            // add_action( 'save_post', array( $this, 'thing_saved' ) );
            add_action( 'save_post_thing', array( $this, 'thing_saved' ) );
            // add_action( 'transition_post_status', array( $this, 'thing_saved' ) );
            debug("Early Bird hooked into save_post_thing");
        }   
        //
        // Check if the user qualifies for points
        public function thing_saved( $user_id ) {
            debug("Early Bird check triggered");
            // Make sure the post being published is our type
            if ( ! isset( $this->prefs['post_type'] ) || 
                $this->prefs['post_type'] != $post->post_type ) return;
            // Check if user is excluded (required)
            if ( $this->core->exclude_user( $user_id ) ) return;
            debug("Early Bird not excluded by user");
            // Check to see if this is a "thing" type 
            debug("This post type:" . $this->prefs['post_type']);
            debug("Post type:" . $post->post_type);
            debug("Post status:" . $post->post_status);
            if ( 'thing' != get_post_type($post) ) return;
            debug("Early Bird type = thing");
            // Check to see if this thing is a draft
            if ($post->post_status != 'draft') return;
            debug("Early Bird status is draft");
            // Check to see if the date qualifies as "early bird" (1st to 15th)
            if (getdate()["mday"] > 20) return;
            debug("Early Bird date checked out");
            // Make sure this is a unique event
            if ( $this->has_entry( 'earlybird', '', $user_id ) ) return;
            debug("Early Bird unique event");
            // Execute
            $this->core->add_creds(
                'earlybird',
                $user_id,
                $this->prefs['creds'],
                $this->prefs['log'],
                0,
                '',
                $m
            );
        }
        //
        // This method is called by myCred in the admin area and must be included if your hook has any settings.
        public function preferences() {
            // Our settings are available under $this->prefs
            $prefs = $this->prefs; ?>

            <!-- First we set the amount -->
            <label class="subheader"><?php echo $this->core->plural(); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" 
                            name="<?php echo $this->field_name( 'creds' ); ?>" 
                            id="<?php echo $this->field_id( 'creds' ); ?>" 
                            value="<?php echo esc_attr( $prefs['creds'] ); ?>" 
                            size="8" />
                    </div>
                </li>
            </ol>
            <!-- Then the log template -->
            <label class="subheader"><?php _e( 'Log template', 'mycred' ); ?></label>
            <ol>
                <li>
                    <div class="h2">
                        <input type="text" 
                            name="<?php echo $this->field_name( 'log' ); ?>" 
                            id="<?php echo $this->field_id( 'log' ); ?>" 
                            value="<?php echo esc_attr( $prefs['log'] ); ?>" 
                            class="long" />
                    </div>
                </li>
            </ol>
            <?php
        }
        //
        // Sanitises the hooks preferences before they are saved in the database.
        public function sanitise_preferences( $data ) {
            $new_data = $data;

            // Apply defaults if any field is left empty
            $new_data['creds'] = ( !empty( $data['creds'] ) ) ? $data['creds'] : $this->defaults['creds'];
            $new_data['log'] = ( !empty( $data['log'] ) ) ? sanitize_text_field( $data['log'] ) : $this->defaults['log'];

            return $new_data;
        }
    }
}
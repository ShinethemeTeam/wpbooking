<div class="wrap">
    <div id="icon-tools" class="icon32"></div>
    <h2><?php _e( "Form Builder" , 'traveler-booking' ) ?></h2>
</div>
<div class="wrap">
    <br class="clear">
    <div class="traveler-row form-build-v2">
        <div class="traveler-col-md-12 head-form">
            <h3 class=""><span>Form fields</span></h3>
        </div>
        <div class="traveler-col-md-12">
           <table class="traveler-select-layout">
               <tr class="traveler_booking_dropdown">
                   <th scope="row">
                       <label for="dropdown">Layout:</label>
                   </th>
                   <td>
                       <select name="traveler_booking_dropdown" class="form-control  min-width-200" id="traveler_booking_dropdown">
                           <option value=""><?php _e("-- Select Layout --",'traveler-booking') ?></option>
                           <optgroup label="Single Car">
                               <option value="volvo">Volvo</option>
                               <option value="saab">Saab</option>
                           </optgroup>
                       </select>
                   </td>
                   <td>
                       <a hidden="#" class="button" >Select</a>
                   </td>
               </tr>
           </table>
        </div>
        <div class="traveler-col-md-7">
            <div class="form-content">
                <?php
                wp_editor(stripslashes(''),'traveler-build'); ?>
            </div>
        </div>
        <div class="traveler-col-md-5">

            <div class="select-control">
                <label for="select_form_help_shortcode" class="control-label">Generate Tag:</label>
                <select name="traveler_booking_dropdown" class="form-control  min-width-200" id="traveler_booking_dropdown">
                    <option value=""><?php _e("-- Select Flied --",'traveler-booking') ?></option>
                    <optgroup label="Single Car">
                        <option value="volvo">Volvo</option>
                        <option value="saab">Saab</option>
                    </optgroup>
                </select>
                <i class="desc">Select option to configure or show help info about tags</i>
            </div>
            <div class="select-control">
                <div class="head"> Contact </div>
                <hr>
                <div class="content-control">
                    <div class="traveler-row">
                        <div class="traveler-col-md-6">
                            <div class="traveler-build-group ">
                                <label class="control-label"><strong>Name</strong> (required):</label>
                                <input type="text"  name="" id="">
                            </div>
                        </div>
                        <div class="traveler-col-md-6">
                            <div class="traveler-build-group ">
                                <label class="control-label"><strong>Default value</strong> (optional):</label>
                                <input type="text"  name="" id="">
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div>
                    <div class="traveler-row">
                        <div class="traveler-col-md-12">
                            <div class="traveler-build-group ">
                                <label class="control-label"><?php _e("Copy and paste this shortcode into the form at left side",'traveler-booking') ?></label>
                                <input type="text"  name="" id="" readonly="readonly">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="traveler-col-md-12">
            <div class="save-control">
                <button type="button" class="button-primary button"><?php _e("Save Layout",'traveler-booking') ?></button>
            </div>
        </div>
    </div>

</div>
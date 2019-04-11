/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */


 jQuery(document).ready(function($) {
     
     $('#edit-search-block-form--2').focus();
     
     $('#edit-search-block-form--2').keyup(function(){
         // if it looks like a barcode then fire off a search
         var term = String($(this).val());
         var reg = /^EGEN[0-9]{7}$/i;
         if(term.match(reg)){
             console.log(this.form.submit());
         }else{
             console.log('not one');
         }
         
     });
     
 });

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - https://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth

(function ($, Drupal, window, document, undefined) {

    
// To understand behaviors, see https://drupal.org/node/756722#behaviors
Drupal.behaviors.my_custom_behavior = {
  attach: function(context, settings) {

    // Place your code here.

  }
};


})(jQuery, Drupal, this, this.document);

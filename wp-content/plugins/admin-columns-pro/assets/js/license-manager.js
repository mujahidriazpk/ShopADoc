'use strict';jQuery(function($){$(document).ready(function(){$('.ac-message [data-hide-notice=license-check]').click(function(){var el=$(this).parents('.ac-message');$(this).after('<div class="spinner right"></div>').show();$(this).hide();$.post(ajaxurl,{'action':'cpac_hide_license_expiry_notice'},function(){el.find('.spinner').hide();el.slideUp()});return false})})});if(typeof cpac_license_check_js=='undefined'){var cpac_license_check_js=true;jQuery(document).ready(function($){$('body').on('click','.cpac-check-license',function(){if(!$(this).hasClass('disabled')){var el=$(this).parents('p');if(el.length==0){el=$(this).parents('.update-message')}$(this).addClass('disabled');$(this).after('<div class="spinner ac-spinner inline"></div>').show();$.post(ajaxurl,{'action':'cpac_check_license_renewed'},function(data){el.find('.spinner').hide();el.parent().replaceWith(data)})}return false})})}
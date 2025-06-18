/**
 * RetinaLogo
 * Contact Form
 * Header Fixed
 * alert box
 */



(function ($) {
  "use strict";

  var themesflatTheme = {
    // Main init function
    init: function () {
      this.config();
      this.events();
    },

    // Define vars for caching
    config: function () {
      this.config = {
        $window: $(window),
        $document: $(document),
      };
    },

    // Events
    events: function () {
      var self = this;

      // Run on document ready
      self.config.$document.on("ready", function () {
        // Retina Logos
        self.retinaLogo();
      });

      // Run on Window Load
      self.config.$window.on("load", function () {});
    },
  }; // end themesflatTheme

  // Start things up
  themesflatTheme.init();

  /* RetinaLogo
  ------------------------------------------------------------------------------------- */
  var retinaLogos = function () {
    var retina = window.devicePixelRatio > 1 ? true : false;
    if (retina) {
      $("#site-logo-inner").find("img").attr({
        src: "assets/images/logo/logo@2x.png",
        width: "197",
        height: "48",
      });

      $("#logo-footer.style").find("img").attr({
        src: "assets/images/logo/logo-footer@2x.png",
        width: "197",
        height: "48",
      });
      $("#logo-footer.style2").find("img").attr({
        src: "assets/images/logo/logo@2x.png",
        width: "197",
        height: "48",
      });
    }
  };

  /* Contact Form
  ------------------------------------------------------------------------------------- */
  var ajaxContactForm = function () {
    $("#contactform").each(function () {
      $(this).validate({
        submitHandler: function (form) {
          var $form = $(form),
            str = $form.serialize(),
            loading = $("<div />", { class: "loading" });

          $.ajax({
            type: "POST",
            url: $form.attr("action"),
            data: str,
            beforeSend: function () {
              $form.find(".send-wrap").append(loading);
            },
            success: function (msg) {
              var result, cls;
              if (msg == "Success") {
                result =
                  "Email Sent Successfully. Thank you, Your application is accepted - we will contact you shortly";
                cls = "msg-success";
              } else {
                result = "Error sending email.";
                cls = "msg-error";
              }
              $form.prepend(
                $("<div />", {
					class: "flat-alert " + cls,
                  text: result,
                }).append(
                  $(
                    '<a class="close" href="#"><i class="icon icon-close2"></i></a>'
                  )
                )
              );

              $form.find(":input").not(".submit").val("");
            },
            complete: function (xhr, status, error_thrown) {
              $form.find(".loading").remove();
            },
          });
        },
      });
    }); // each contactform
  };
  /* Header Fixed
  ------------------------------------------------------------------------------------- */
  var headerFixed = function () {
    if ($("header").hasClass("header-fixed")) {
      var nav = $("#header");
      if (nav.length) {
        var offsetTop = nav.offset().top,
          headerHeight = nav.height(),
          injectSpace = $("<div>", {
            height: headerHeight,
          });
        injectSpace.hide();

        $(window).on("load scroll", function () {
          if ($(window).scrollTop() > 0) {
            nav.addClass("is-fixed");
            injectSpace.show();
            $("#trans-logo").attr("src", "images/logo/logo@2x.png");
          } else {
            nav.removeClass("is-fixed");
            injectSpace.hide();
            $("#trans-logo").attr("src", "images/logo/logo-footer@2x.png");
          }
        });
      }
    }
  };

  $("#showlogo").prepend(
    '<a href="index.html"><img id="theImg" src="assets/images/logo/logo2.png" /></a>'
  );

  // =========NICE SELECT=========
  $(".select_js").niceSelect();

  new WOW().init();

  //Submenu Dropdown Toggle
  if ($(".main-header li.dropdown2 ul").length) {
    $(".main-header li.dropdown2").append('<div class="dropdown2-btn"></div>');

    //Dropdown Button
    $(".main-header li.dropdown2 .dropdown2-btn").on("click", function () {
      $(this).prev("ul").slideToggle(500);
    });

    //Disable dropdown parent link
    $(".navigation li.dropdown2 > a").on("click", function (e) {
      e.preventDefault();
    });

    //Disable dropdown parent link
    $(
      ".main-header .navigation li.dropdown2 > a,.hidden-bar .side-menu li.dropdown2 > a"
    ).on("click", function (e) {
      e.preventDefault();
    });

    $(".price-block .features .arrow").on("click", function (e) {
      $(e.target.offsetParent.offsetParent.offsetParent).toggleClass(
        "active-show-hidden"
      );
    });
  }

  // Mobile Nav Hide Show
  if ($(".mobile-menu").length) {
    //$('.mobile-menu .menu-box').mCustomScrollbar();

    var mobileMenuContent = $(".main-header .nav-outer .main-menu").html();
    $(".mobile-menu .menu-box .menu-outer").append(mobileMenuContent);
    $(".sticky-header .main-menu").append(mobileMenuContent);

    //Hide / Show Submenu
    $(".mobile-menu .navigation > li.dropdown2 > .dropdown2-btn").on(
      "click",
      function (e) {
        e.preventDefault();
        var target = $(this).parent("li").children("ul");
        var args = { duration: 300 };
        if ($(target).is(":visible")) {
          $(this).parent("li").removeClass("open");
          $(target).slideUp(args);
          $(this)
            .parents(".navigation")
            .children("li.dropdown2")
            .removeClass("open");
          $(this)
            .parents(".navigation")
            .children("li.dropdown2 > ul")
            .slideUp(args);
          return false;
        } else {
          $(this)
            .parents(".navigation")
            .children("li.dropdown2")
            .removeClass("open");
          $(this)
            .parents(".navigation")
            .children("li.dropdown2")
            .children("ul")
            .slideUp(args);
          $(this).parent("li").toggleClass("open");
          $(this).parent("li").children("ul").slideToggle(args);
        }
      }
    );

    //3rd Level Nav
    $(
      ".mobile-menu .navigation > li.dropdown2 > ul  > li.dropdown2 > .dropdown2-btn"
    ).on("click", function (e) {
      e.preventDefault();
      var targetInner = $(this).parent("li").children("ul");

      if ($(targetInner).is(":visible")) {
        $(this).parent("li").removeClass("open");
        $(targetInner).slideUp(500);
        $(this)
          .parents(".navigation > ul")
          .find("li.dropdown2")
          .removeClass("open");
        $(this)
          .parents(".navigation > ul")
          .find("li.dropdown > ul")
          .slideUp(500);
        return false;
      } else {
        $(this)
          .parents(".navigation > ul")
          .find("li.dropdown2")
          .removeClass("open");
        $(this)
          .parents(".navigation > ul")
          .find("li.dropdown2 > ul")
          .slideUp(500);
        $(this).parent("li").toggleClass("open");
        $(this).parent("li").children("ul").slideToggle(500);
      }
    });

    //Menu Toggle Btn
    $(".mobile-nav-toggler").on("click", function () {
      $("body").addClass("mobile-menu-visible");
    });

    //Menu Toggle Btn
    $(".mobile-menu .menu-backdrop, .close-btn").on("click", function () {
      $("body").removeClass("mobile-menu-visible");
      $(".mobile-menu .navigation > li").removeClass("open");
      $(".mobile-menu .navigation li ul").slideUp(0);
    });

    $(document).keydown(function (e) {
      if (e.keyCode === 27) {
        $("body").removeClass("mobile-menu-visible");
        $(".mobile-menu .navigation > li").removeClass("open");
        $(".mobile-menu .navigation li ul").slideUp(0);
      }
    });
  }

  var ajaxSubscribe = {
    obj: {
      subscribeEmail: $("#subscribe-email"),
      subscribeButton: $("#subscribe-button"),
      subscribeMsg: $("#subscribe-msg"),
      subscribeContent: $("#subscribe-content"),
      dataMailchimp: $("#subscribe-form").attr("data-mailchimp"),
      success_message:
        '<div class="notification_ok">Thank you for joining our mailing list! Please check your email for a confirmation link.</div>',
      failure_message:
        '<div class="notification_error">Error! <strong>There was a problem processing your submission.</strong></div>',
      noticeError: '<div class="notification_error">{msg}</div>',
      noticeInfo: '<div class="notification_error">{msg}</div>',
      basicAction: "mail/subscribe.php",
      mailChimpAction: "mail/subscribe-mailchimp.php",
    },

    eventLoad: function () {
      var objUse = ajaxSubscribe.obj;

      $(objUse.subscribeButton).on("click", function () {
        if (window.ajaxCalling) return;
        var isMailchimp = objUse.dataMailchimp === "true";

        if (isMailchimp) {
          ajaxSubscribe.ajaxCall(objUse.mailChimpAction);
        } else {
          ajaxSubscribe.ajaxCall(objUse.basicAction);
        }
      });
    },

    ajaxCall: function (action) {
      window.ajaxCalling = true;
      var objUse = ajaxSubscribe.obj;
      var messageDiv = objUse.subscribeMsg.html("").hide();
      $.ajax({
        url: action,
        type: "POST",
        dataType: "json",
        data: {
          subscribeEmail: objUse.subscribeEmail.val(),
        },
        success: function (responseData, textStatus, jqXHR) {
          if (responseData.status) {
            objUse.subscribeContent.fadeOut(500, function () {
              messageDiv.html(objUse.success_message).fadeIn(500);
            });
          } else {
            switch (responseData.msg) {
              case "email-required":
                messageDiv.html(
                  objUse.noticeError.replace(
                    "{msg}",
                    "Error! <strong>Email is required.</strong>"
                  )
                );
                break;
              case "email-err":
                messageDiv.html(
                  objUse.noticeError.replace(
                    "{msg}",
                    "Error! <strong>Email invalid.</strong>"
                  )
                );
                break;
              case "duplicate":
                messageDiv.html(
                  objUse.noticeError.replace(
                    "{msg}",
                    "Error! <strong>Email is duplicate.</strong>"
                  )
                );
                break;
              case "filewrite":
                messageDiv.html(
                  objUse.noticeInfo.replace(
                    "{msg}",
                    "Error! <strong>Mail list file is open.</strong>"
                  )
                );
                break;
              case "undefined":
                messageDiv.html(
                  objUse.noticeInfo.replace(
                    "{msg}",
                    "Error! <strong>undefined error.</strong>"
                  )
                );
                break;
              case "api-error":
                objUse.subscribeContent.fadeOut(500, function () {
                  messageDiv.html(objUse.failure_message);
                });
            }
            messageDiv.fadeIn(500);
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert("Connection error");
        },
        complete: function (data) {
          window.ajaxCalling = false;
        },
      });
    },
  };

  /* alert box
  ------------------------------------------------------------------------------------- */
  var alertBox = function () {
    $(document).on("click", ".close", function (e) {
      $(this).closest(".flat-alert").remove();
      e.preventDefault();
    });
  };

  // Dom Ready
  $(function () {
    $(window).on("load resize", function () {
      retinaLogos();
    });
    headerFixed();
    ajaxContactForm();
    ajaxSubscribe.eventLoad();
    alertBox();
  });
})(jQuery);

$(document).on('change','input:file', function BTN_UPLOAD(e) { 
//$("input:file").on('change', function BTN_UPLOAD(){
		debugger;
        var FILES = $(this).prop('files');   
		
		$.each(FILES, function( index, FILE ) {
		  
		
			var form_data = new FormData();  
			
			var other_data = $('form').serializeArray();
			$.each(other_data,function(key,input){
				form_data.append(input.name,input.value);
			});
			
			 var OBJ = {"ID":e.currentTarget.id};
			 
			
			
			form_data.append('file', FILE);
			form_data.append("SENDER",e.currentTarget.id);
			form_data.append("ID",$('#X_ID').val());
			
			$.ajax({
				url: 'PHP/upload.php', 
				dataType: 'text',
				cache: false,
				contentType: false,
				processData: false,
				data: form_data,                         
				type: 'post',
				success: function(response){
					//alert(response);
					//alert('File uploaded OK.');
					response = $.parseJSON(response);
					if (response.ERR.length > 0 ){SHOW_ALERT(response.ERR, 'body');}
					SHOW_ALERT('File uploaded OK.', 'body');
					
					$('#TXT_PROP_MAIN_PIC_PATH').text(response.PATH);
					
					$('#TXT_B_AVT_PIC_PATH').val(response.PATH);
					$('.AVATAR_PIC').attr("src","");
					$('.AVATAR_PIC').attr("src",response.PATH);
					
				},
				error: function(response){
					SHOW_ALERT("error : " + JSON.stringify(response), 'body');
				}
			});
		});
});
	
$(".BTN_ADD_PROPERTY").on('click', function BTN_ADD_PROPERTY(){
	

    // DATOS
	
	var OBJ = {};
	
	OBJ['Datos'] = FORM_2_OBJ('FRM_INFORMATION');
	
    OBJ['R'] = 1;
	OBJ['tabla'] = 'PROPS';
	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    var URL = RESP.DATOS;

});

$(document).on('click', ".BTN_SEND_CODE", function BTN_SEND_CODE(e) {
		
	e.preventDefault();
		
	disableButton('BTN_SEND_CODE');
	
	var OBJ = {};
	
    // DATOS
	OBJ['Datos'] = FORM_2_OBJ('FRM_REGISTER'); 

    OBJ['R'] = 'BTN_SEND_CODE';
	OBJ['tabla'] = 'Users';
	
	//console.log(OBJ);
		
    // VERIFICACIONES  
 
    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    var URL = RESP.DATOS;	
	
	if ( RESP.PROMPT != '' ) {SHOW_ALERT_CLASS(RESP.PROMPT,'modal-content');}	
	
	$('input[name="TXT_REG_NAME"]').val(RESP.WA_USER_NAME); 

});

$(document).on('click', ".BTN_SEND_CODE_GUESTS", function BTN_SEND_CODE(e) {
		
	e.preventDefault();
		
	disableButton('BTN_SEND_CODE_GUESTS');
	
	var OBJ = {};
	
    // DATOS
	OBJ['Datos'] = FORM_2_OBJ('FRM_REGISTER'); 

    OBJ['R'] = 'BTN_SEND_CODE_GUESTS';
	OBJ['tabla'] = 'GUESTS';
	
	//console.log(OBJ);
		
    // VERIFICACIONES  
 
    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    var URL = RESP.DATOS;	
	
	if ( RESP.PROMPT != '' ) {SHOW_ALERT_CLASS(RESP.PROMPT,'modal-content');}	
	
	$('input[name="TXT_REG_NAME"]').val(RESP.WA_USER_NAME); 

});


$(document).on('keyup', ".TXT_REG_CODE", function TXT_REG_CODE(e) {
		
	e.preventDefault();
	
	var OBJ = {};
	
    // DATOS
	OBJ['Datos'] = FORM_2_OBJ('FRM_REGISTER'); 

    OBJ['R'] = 7;
	OBJ['tabla'] = 'Users';
	
	//console.log(OBJ);
		
    // VERIFICACIONES  
 
    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    //var DATOS = RESP.DATOS;	
	
	//var SESSION = RESP.SESSION;	
	
	
	
	if ( RESP.SESSION.REG_STEP_1 == true ) { 
		
			
		$("#REG_STEP_2").show(); $("#REG_STEP_1").hide(); 
		
		
	}
	
	
});

$(document).on('click', ".BTN_REG_LOGIN", function BTN_REG_LOGIN(e) {
		e.preventDefault();
});

$(document).on('click', ".BTN_REGISTER", function BTN_REGISTER(e) {
	
	e.preventDefault();
	
	var OBJ = {};
	
    // DATOS
	OBJ['Datos'] = FORM_2_OBJ('FRM_REGISTER'); 

    OBJ['R'] = 'BTN_REGISTER';
	OBJ['tabla'] = 'Users';
	
	//console.log(OBJ);
		
    // VERIFICACIONES  
 
    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
	
	if ( RESP.PROMPT != undefined ) { SHOW_ALERT_CLASS(RESP.PROMPT, 'modal-content'); return;}
	
	if ( RESP.SESSION.REG_STEP_2 == true ) { 
		
		$("#REG_STEP_2").hide(); 
		$("#REG_STEP_3").show();  
		$(".POP_TITLE_REGISTER").text('Success!');
	}
	
	
	
});

$(document).on('click', ".BTN_REGISTER_GUESTS", function BTN_REGISTER(e) {
	
	e.preventDefault();
	
	var OBJ = {};
	
    // DATOS
	OBJ['Datos'] = FORM_2_OBJ('FRM_REGISTER'); 

    OBJ['R'] = 'BTN_REGISTER_GUESTS';
	OBJ['tabla'] = 'GUESTS';
	
	//console.log(OBJ);
		
    // VERIFICACIONES  
 
    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
	
	if ( RESP.PROMPT != undefined ) { SHOW_ALERT_CLASS(RESP.PROMPT, 'modal-content'); return;}
	
	if ( RESP.SESSION.REG_STEP_2 == true ) { 
		
		$("#REG_STEP_2").hide(); 
		$("#REG_STEP_3").show();  
		$(".POP_TITLE_REGISTER").text('Success!');
	}
	
	
	
});




$(document).on('click', ".BTN_LOGIN", function BTN_LOGIN(e) {
	e.preventDefault();
	var OBJ = {};
	
	OBJ['Datos'] = FORM_2_OBJ('FRM_LOGIN');
	
    OBJ['R'] = 9;
	OBJ['tabla'] = 'Users';
	
	//console.log(OBJ);
		
    // VERIFICACIONES  
 
    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
	
	
	if ( RESP.PROMPT != undefined ) { SHOW_ALERT(RESP.PROMPT, 'FRM_LOGIN'); return;}
	
	location.reload();
	
	//IS_LOGIN();
	
});

$(document).on('click', ".BTN_LOGIN_GUESTS", function BTN_LOGIN_GUESTS(e) {
	
	debugger;
	
	e.preventDefault();
	var OBJ = {};
	
	
	OBJ['Datos'] = FORM_2_OBJ('FRM_LOGIN');
	
    OBJ['R'] = 'BTN_LOGIN_GUESTS';
	OBJ['tabla'] = 'GUESTS';
	
	//console.log(OBJ);
		
    // VERIFICACIONES  
 
    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;

	var RESP = POST(URL, datos);
	
	debugger;
    // FRASEO RESPUESTA
	
	
	if ( RESP.PROMPT != undefined ) { SHOW_ALERT(RESP.PROMPT, 'FRM_LOGIN'); return;}
	
	location.reload();
	
	//IS_LOGIN();
	
});






$(document).on('ready','form', function(e) {
				
			  e.preventDefault();
				
}); 



$(document).on('ready', '.TOP_MENU_FIX', function DOCUMENT_READY(e) {


});
	
	
$(document).on('ready', function DOCUMENT_READY(e) {
				
	
				
}); 
	

function POST(url, data) {
    //data = $.parseJSON(data);

    var result;
	
	hideOverlay();
	
    $.ajax({
        type: "POST",
        async: false,
        url: url,
        data: data,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (response) {
            console.log("Data successfully sent. Server responded with:", response);
            result = response;
            return response;
		},
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error:", textStatus, errorThrown);
			hideOverlay();
		},
		complete: function() {
            // Hide the overlay after the AJAX call completes (success or error)
            hideOverlay();
        }
	});
    return result;
}

function POST_v2(OBJ) {
    //data = $.parseJSON(data);
    var OBJ_STR = JSON.stringify(OBJ);

    var data = `${OBJ_STR}`;
	
    var result;
	showOverlay();
	
    $.ajax({
        type: "POST",
        async: false,
        url: './PHP/ajax.php',
        data: data,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (response) {
            console.log("Data successfully sent. Server responded with:", response);
            result = response;
            return response;
		},
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error:", textStatus, errorThrown);
			hideOverlay();
		},
		complete: function() {
            // Hide the overlay after the AJAX call completes (success or error)
            hideOverlay();
        }
	});
    return result;
}

function POST_API(OBJ, API, esperar = false) {
    //data = $.parseJSON(data);
    var OBJ_STR = JSON.stringify(OBJ);


    var data = `${OBJ_STR}`;
	debugger;
    var result;
	
	showOverlay();
	
    $.ajax({
        type: "POST",
        async: esperar, // false = espera hasta que se complete
        url: './' + API,
        data: data,
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        success: function (response) {
            console.log("Data successfully sent. Server responded with:", response);
            result = response;
            return response;
		},
        error: function (jqXHR, textStatus, errorThrown) {
            console.log("Error:", textStatus, errorThrown);
			hideOverlay();
		},
		complete: function() {
            // Hide the overlay after the AJAX call completes (success or error)
            hideOverlay();
        }
	});
	
    return result;
}


function POST_API_v2(OBJ, API, esperar = false) {
    // Convert the object to a JSON string
    var OBJ_STR = JSON.stringify(OBJ);
    var data = `${OBJ_STR}`;

    // Show overlay before making the AJAX call
    showOverlay();

    // Return a promise
    return new Promise((resolve, reject) => {
        $.ajax({
            type: "POST",
            async: esperar, // false = waits until the request completes
            url: './' + API,
            data: data,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            success: function (response) {
                console.log("Data successfully sent. Server responded with:", response);
                resolve(response); // Resolve the promise with the response
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log("Error:", textStatus, errorThrown);
                reject(errorThrown); // Reject the promise with the error
            },
            complete: function() {
                // Hide the overlay after the AJAX call completes (success or error)
                hideOverlay();
            }
        });
    });
}

// Function to show the overlay
function showOverlay() {
    $('#DIV_LOADING').fadeIn(); // Show the overlay with a fade-in effect
}

// Function to hide the overlay
function hideOverlay() {
    $('#DIV_LOADING').fadeOut(); // Hide the overlay with a fade-out effect
}


function LOGIN_SESSION(S_ID) {
	
	var OBJ = {};
	
    OBJ['R'] = '0';
	OBJ['tabla'] = 'Brokers';
	OBJ['S_ID'] = S_ID;

	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
	
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	var RESP = POST(URL, datos);
	
}


function LOAD_MODALS() {
    // DATOS
	
	var OBJ = {};
	
    OBJ['R'] = 5;
	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    var DATOS = RESP.DATOS;
	
	$('#' + 'MODALS').append(DATOS); 
	

	
	
}

function LOAD_TOP_MENU() {
    // DATOS
	
	var OBJ = {};
	
    OBJ['R'] = 4;
	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    var DATOS = RESP.DATOS;
	
	$('#' + 'TOP_MENU').append(DATOS); 
	LOAD_MODALS();
	

	

}

function LOAD_CONTENT(EL_ID, ACTION, PROP_ID = 0) {
    // DATOS
	
	var OBJ = {};
	
    OBJ['R'] = ACTION;
	OBJ['tabla'] = 'PROPS';
	OBJ['prop_id'] = PROP_ID;
	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    var DATOS = RESP.DATOS;
	
	$('#' + EL_ID).append(DATOS); 
	

	
	window.setTimeout(LOAD_MAP(RESP.PROP_LAT,RESP.PROP_LON), 1000);
	
	
}

function LOAD_MAP(LAT, LON) {
	
			if ($("#" + 'map-single').length > 0 ) {} else {return;}; 
		  
				LAT = +LAT;
				LON = +LON;
				
			  // The location of your place
			  var place = {lat: LAT, lng: LON};
			  // The map, centered at your place
			  var map = new google.maps.Map(
				  document.getElementById('map-single'), {zoom: 18, center: place});
			  // The marker, positioned at your place
			  var marker = new google.maps.Marker({position: place, map: map});	
		

		  
}







$(".UPDATE_SAMPLE").on('change', function TARJETA_SAMPLE(e){
	
	var MAIN_IMG = $('#TXT_PROP_MAIN_PIC_PATH').text();
	
	console.log(this.value);
	
	var OBJ = FORM_2_OBJ('FRM_INFORMATION');
	
	var PROP_OBJ = jQuery.parseJSON( OBJ );
	
	   
 
	var TARJETA_SAMPLE_OUT =`
							<div class="homeya-box">
								<div class="archive-top">
									<a href="property-details-v1.html" class="images-group">
										<div class="images-style">
											<img src="${PROP_OBJ['TXT_PROP_MAIN_PIC']}" onerror="this.onerror=null; this.src=\'../images/Default.jpg\'" alt="" width="300" height="400"> 
										</div>
										<div class="top">
											<ul class="d-flex gap-8">
												<li class="flag-tag success">Featured</li>
												<li class="flag-tag style-1">For Sale</li>
											</ul>
											<ul class="d-flex gap-4">
												<li class="box-icon w-32">
													<span class="icon icon-arrLeftRight"></span>
												</li>
												<li class="box-icon w-32">
													<span class="icon icon-heart"></span>
												</li>
												<li class="box-icon w-32">
													<span class="icon icon-eye"></span>
												</li>
											</ul>
										</div>
										<div class="bottom">
											<span class="flag-tag style-2">%s</span>
										</div>
									</a>
									<div class="content">
										<div class="h7 text-capitalize fw-7"><a href="property-details-v1.html" class="link"> %s</a></div>
										<div class="desc"><i class="fs-16 icon icon-mapPin"></i><p>%s</p> </div>
										<ul class="meta-list">
											<li class="item">
												<i class="icon icon-bed"></i>
												<span>%s</span>
											</li>
											<li class="item">
												<i class="icon icon-bathtub"></i>
												<span>%s</span>
												</li>
											<li class="item">
												<i class="icon icon-ruler"></i>
												<span>%s m2</span>
											</li>
										</ul>
									</div>
								</div>
								<div class="archive-bottom d-flex justify-content-between align-items-center">
									<div class="d-flex gap-8 align-items-center">
										<div class="avatar avt-40 round">
											<img src="images/avatar/avt-6.jpg" alt="avt">
										</div>
										<span>%s</span>
									</div>
									<div class="d-flex align-items-center">
										<h6>$ %s</h6>
										<span class="text-variant-1"> %s</span>
									</div>
								</div>
							</div>
						`;	
						
						
	$('#TARJETA_PROP_SAMPLE').empty().append(TARJETA_SAMPLE_OUT);

});


function waitForElm(selector) {
    return new Promise(resolve => {
        if (document.querySelector(selector)) {
            return resolve(document.querySelector(selector));
        }

        const observer = new MutationObserver(mutations => {
            if (document.querySelector(selector)) {
                observer.disconnect();
                resolve(document.querySelector(selector));
            }
        });

        // If you get "parameter 1 is not of type 'Node'" error, see https://stackoverflow.com/a/77855838/492336
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
}

function disableButton(buttonId) {
    var $button = $('#' + buttonId), // jQuery object of the button
        countdown = 60; // 60 seconds countdown

    // Disable the button
    $button.prop('disabled', true);

    // Start the countdown
    var intervalId = setInterval(function() {
        // Update the button text
        $button.text('Sent. ' + countdown + 's');

        // Decrease the countdown
        countdown--;

        // If countdown is 0, enable the button and stop the interval
        if (countdown < 0) {
            // Enable the button
            $button.prop('disabled', false);

            // Reset the button text
            $button.text('Send again');

            // Clear the interval
            clearInterval(intervalId);
        }
    }, 1000); // 1000 milliseconds = 1 second
}

function IS_LOGIN(){
	
	if ( $('.BOX_OPTIONS').length ) {  
	
			//$(".TOP_MENU_FIX").css("display", "block");
			
			$(".DRP_BROKER_CONTACT").show();
			$(".DRP_BROKER").hide();
		  
		  $.ajax({
			  method: 'POST',
			url: '../PHP/IS_LOGIN.php',
			data: "",
			//async: true,
			dataType: 'json',
			success: function (RESP) {
			  //alert(data);
			  if (RESP.SESSION.LOGIN){
			  
					$(".BOX_OPTIONS").hide();
					$(".BOX_USER").show();		
					$(".U_ID").text(RESP.SESSION.USER.NAME);					
					
					// USUARIO ES BROKER
						
						if (RESP.SESSION.USER.IS_BROKER){
							$(".BOX_USER").hide();
							$(".BOX_BROKER").show();
							$(".DRP_BROKER").show();
							$(".DRP_BROKER_CONTACT").hide();
							$(".B_ID").text(RESP.SESSION.BROKER.DATA[0].NAME);
							$(".B_MY_TITLE").text('I am Broker');
						}else{
							$(".BOX_BROKER").show();
							$(".DRP_BROKER_CONTACT").show();
							$(".DRP_BROKER").hide();
							$(".B_ID").text(RESP.SESSION.BROKER.DATA[0].NAME);			
							$(".BOX_BROKER").show();
							$(".DRP_BROKER_CONTACT").show();							
						
						}
			  
					
					
					

					
					
			  }else{

					$(".B_ID").text(RESP.SESSION.BROKER.DATA[0].NAME);
					$("#modalLogin").modal('hide');
					$(".BOX_OPTIONS").show();
					$(".BOX_USER").hide();
					$(".U_ID").text('');
			  
			  
			  }  
			}
		  });	
		  
	}
}

function SHOW_ALERT_V3(message) {
    // Create a new div
    var alertDiv = $('<div class="" ></div>');
  
    // Add classes and message
    alertDiv.addClass('alert').addClass('alert3').text(message);
	
	
    // Append to body
    //$('body').append(alertDiv);
    $('body').prepend(alertDiv);
    //$("#" + TO_ELEMENT).prepend(alertDiv);
	
	$('#sub-body').addClass('blur');
	
	alertDiv.addClass('unblur');

    // Show the alert and then fade out
    alertDiv.fadeIn(200).delay(3000).fadeOut(500, function() {
        $(this).remove();
		location.reload();
		
    });
}

function SHOW_ALERT(message, TO_ELEMENT) {
    // Create a new div
    var alertDiv = $('<div class="" ></div>');
  
    // Add classes and message
    alertDiv.addClass('alert2').text(message);

    // Append to body
    //$('body').append(alertDiv);
    $("#" + TO_ELEMENT).prepend(alertDiv);

    // Show the alert and then fade out
    alertDiv.fadeIn(200).delay(3000).fadeOut(500, function() {
        $(this).remove();
    });
}

function SHOW_ALERT_CLASS(message, TO_ELEMENT) {
    // Create a new div
    var alertDiv = $('<div class="" ></div>');
  
    // Add classes and message
    alertDiv.addClass('alert2').text(message);

    // Append to body
    //$('body').append(alertDiv);
    $("." + TO_ELEMENT).prepend(alertDiv);

    // Show the alert and then fade out
    alertDiv.fadeIn(200).delay(3000).fadeOut(500, function() {
        $(this).remove();
    });
}

$(document).on('click','.BTN_PROFILE_SAVE', function BTN_PROFILE_SAVE(e) { 
				
	e.preventDefault();
	
	var OBJ = {}; 
	
	OBJ['Datos'] = FORM_2_OBJ('FRM_PROFILE');
	
    OBJ['R'] = 10;
	OBJ['tabla'] = 'Brokers';
	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    var URL = RESP.DATOS;
	
	$( "#B_PROFILE_CONTENT" ).empty();
	$('#B_PROFILE_CONTENT').load('../PHP/HTML_PROFILE_FORMER.php');
	$('#TOP_MENU').load('../PHP/HTML_TOP_MENU_2.php');
	//LOAD_CONTENT('B_PROFILE_CONTENT',2.1); 
	 			
}); 


function WAIT_FOR_EL(selector) {
    return new Promise(resolve => {
        if (document.querySelector(selector)) {
            return resolve(document.querySelector(selector));
        }

        const observer = new MutationObserver(mutations => {
            if (document.querySelector(selector)) {
                observer.disconnect();
                resolve(document.querySelector(selector));
            }
        });

        // If you get "parameter 1 is not of type 'Node'" error, see https://stackoverflow.com/a/77855838/492336
        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
}


$(document).on('click','.BTN_SAVE', function BTN_SAVE(e) { 
				
	e.preventDefault();
	

	
	
	var OBJ = {}; 
	
	OBJ['Datos'] = FORM_2_OBJ(this.attributes.frm.value);
	
    OBJ['R'] = 'BTN_SAVE';
	OBJ['tabla'] = this.attributes.db.value ;
	
	//console.log(OBJ);
		
    // VERIFICACIONES
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	debugger;
	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    var URL = RESP.DATOS;
	//$( "#B_PROFILE_CONTENT" ).empty();
	//$('#TOP_MENU').load('../PHP/HTML_TOP_MENU_2.php');
	//$('#B_PROFILE_CONTENT').load('../PHP/' + this.attributes.after.value + '.php');
	
	location.reload();
	

	//LOAD_CONTENT('B_PROFILE_CONTENT',2.1); 
	 			
}); 

$(document).on('click','.BTN_SAVE_TO_ID', function BTN_SAVE_TO_ID(e) { 
				
	e.preventDefault();
	
	var OBJ = {}; 
	
	OBJ['Datos'] = FORM_2_OBJ(this.attributes.frm.value);
	
    OBJ['R'] = 'BTN_SAVE_TO_ID';
	//OBJ['tabla'] = this.attributes.db.value ;
	OBJ['id'] = $('#X_ID').val() ;
	
	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    
	debugger;
	
	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    var URL = RESP.DATOS;
	
	$( "#B_PROFILE_CONTENT" ).empty();
	//$('#TOP_MENU').load('../PHP/HTML_TOP_MENU_2.php');
	$('#B_PROFILE_CONTENT').load('../PHP/' + this.attributes.after.value + '.php');

	//LOAD_CONTENT('B_PROFILE_CONTENT',2.1); 
	 			
}); 

$(document).on('click','.BTN_NUEVO_EVENTO', function BTN_NUEVO(e) { 

	e.preventDefault();
	
	$("#FRM_EVENT input").val("");
	// LLENA DROPDOWNS
	
	for(i = 1; i <= 4; i++) { 
		
		let ddSelect;
		ddSelect = document.getElementById('#TIMEPO_' + i + '_0').msDropdown; ddSelect.selectedIndex = 0;
		ddSelect = document.getElementById('#TIMEPO_' + i + '_1').msDropdown; ddSelect.selectedIndex = 0;
		ddSelect = document.getElementById('#TIMEPO_' + i + '_2').msDropdown; ddSelect.selectedIndex = 0;
		ddSelect = document.getElementById('#TIMEPO_' + i + '_3').msDropdown; ddSelect.selectedIndex = 0;
		ddSelect = document.getElementById('#TIMEPO_' + i + '_4').msDropdown; ddSelect.selectedIndex = 0;
		
	}
	
	return;
	
	
	var qrcode = new QRCode(document.getElementById("qrcode"), {
		text: $('#CUSTOM_LINK').val(),
		width: 128,
		height: 128,
		colorDark : "#000000",
		colorLight : "#ffffff",
		correctLevel : QRCode.CorrectLevel.H
	});
	


}); 

function TOGGLE(el) {
  var x = document.getElementById(el);
  if (x.style.display === "none") {
    x.style.display = "block";
	
	$('html, body').animate({scrollTop: $('#' + el).offset().top}, 5);
	
  } else {
    x.style.display = "none";
  }
}

function REMOVE_X(e,ID){
	
	
	
	var OBJ = {}; 
	
	OBJ['Datos'] = '';
	
    OBJ['R'] = 11;
	OBJ['tabla'] = e.attributes.db.value ;
	OBJ['ID'] = ID;
	
	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	var RESP = POST(URL, datos);
	
	debugger;
	
    // FRASEO RESPUESTA
	//location.reload();
	
	//$('#B_PROFILE_CONTENT').load('../PHP/' + e.attributes.after.value + '.php');
	
}

$(document).on('click','.LNK_FORGOT_PWD', function LNK_FORGOT_PWD(e) { 
				
	e.preventDefault();
	
	var OBJ = {}; 
	
	OBJ['Datos'] = FORM_2_OBJ('FRM_LOGIN');
	
    OBJ['R'] = 'FORGOT_PWD';
    //OBJ['R'] = 12;


	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	var RESP = POST(URL, datos);
	
    // FRASEO RESPUESTA
    //var URL = RESP.DATOS;
	
	
	if ( RESP.PROMPT != '' ) {SHOW_ALERT(RESP.PROMPT,'FRM_LOGIN');}	
	
	//$( "#B_PROFILE_CONTENT" ).empty();
	//$('#B_PROFILE_CONTENT').load('../PHP/HTML_PROFILE_FORMER.php');
	//$('#TOP_MENU').load('../PHP/HTML_TOP_MENU_2.php');
	//LOAD_CONTENT('B_PROFILE_CONTENT',2.1); 
	 			
}); 

$(document).on('click','.icon-pass', function SHOW_PWD(e) {
//$(".SHOW_PWD").click(function() {	
	var passwordInputs = document.getElementsByClassName('password-field');
	e.currentTarget.parentElement.parentElement
	$.each(passwordInputs, function (i) {
	
		if (passwordInputs[i].type === 'password') {
			passwordInputs[i].type = 'text';
			//e.target.classList.add('fa-eye-slash'); // change the icon to 'eye-slash' when the password is revealed
			//e.target.classList.remove('fa-eye');
		} else {
			passwordInputs[i].type = 'password';
			//e.target.classList.add('fa-eye'); // change the icon back to 'eye' when the password is hidden
			//e.target.classList.remove('fa-eye-slash');
		}
		
		// toggle the type attribute
		//const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
		//password.setAttribute('type', type);
		// toggle the eye slash icon
		//this.classList.toggle('fa-eye-slash');
		
		
	});


}); 


function SHOW_ALERT(message, TO_ELEMENT) {
    // Create a new div
    var alertDiv = $('<div class="" ></div>');
  
    // Add classes and message
    alertDiv.addClass('alert2').text(message);

    // Append to body
    //$('body').append(alertDiv);
    $("#" + TO_ELEMENT).prepend(alertDiv);

    // Show the alert and then fade out
    alertDiv.fadeIn(200).delay(3000).fadeOut(500, function() {
        $(this).remove();
    });
}

$(document).on('click', ".BTN_REG_LOGIN", function BTN_REG_LOGIN(e) {
		e.preventDefault();
		
		$("#REG_STEP_1").show(); 
		$("#REG_STEP_2").hide(); 
		$("#REG_STEP_3").hide(); 
		
});




function FORM_2_OBJ(ID){
	
	
	if (typeof tinyMCE !== 'undefined') { 
        tinyMCE.triggerSave();
    }
		
	var e = document.getElementById(ID);
	var inputs = e; 
	var obj = {};
	obj.OPTIONS 		= [] ;
	obj.RESTRICTIONS 	= [] ;
	//obj.PLATILLOS 		= [] ;
	
	//obj.SELECTED = [] ;
	
	
	
	//obj.OPTIONS.push({"A": "1"}) ; 
	
	for (var i = 0; i < inputs.length; i++) {
	
		
		// id
		
		obj['id'] = $('#X_ID').val();
		

		
		// DROPDOWN CON IMAGENES 
		//debugger;
		if (inputs[i].classList.contains('ms-value-input')){	 
			//obj['OPTION'][inputs[i].name] = inputs[i].value;
			 var T = {};
			 T[inputs[i].name] = inputs[i].value;
			obj.OPTIONS.push(T) ;    
		
		}		
	
		// INPUTS
		//if (inputs[i].name && inputs[i].className != 'ms-value-input') {
		
		var TYPE = inputs[i].type;
		var TYPES = ['text', 'number', 'email'   ];
		
		if (inputs[i].name && TYPES.includes(TYPE) ) {
			 //debugger;
			
			//var path = inputs[i].attributes.padre.value;   
			
			//if (path){
				obj[inputs[i].name] = inputs[i].value;
			//}else{

			//}
		}

		if (inputs[i].type != 'radio') {
			//obj[inputs[i].name] = inputs[i].value;
			//obj.SELECTED.push(inputs[i].value) ; 
		}		 
 
		
		
		if (inputs[i].type == 'select-one'){
			obj[inputs[i].name] = inputs[i].value; 
		}
		
		
		// RADIO
		
		if (inputs[i].type == 'radio'){
			if ( inputs[i].checked && inputs[i].attributes['d_id'].value != 'on') {
			//debugger;
				
				obj[inputs[i].name] = inputs[i].attributes['d_id'].value; 
				
				var TEMP_OBJ= {};
				TEMP_OBJ[inputs[i].name] = inputs[i].attributes['d_id'].value;
				//obj.PLATILLOS.push(inputs[i].attributes['d_id'].value);

			}
		}
		
		
		// DROPDOWNS
		if (inputs[i].children[1]){
			if (inputs[i].children[1].className == "nice-select"){
			
				
				//obj[inputs[i].name] = inputs[i].children[1].children[0].innerHTML;
				
			}
		}
		
		
		
		// CHECK BOXES

			if (inputs[i].type == 'checkbox'){
				
				//debugger;
				//obj[inputs[i].name] = $('.box-fieldset .nice-select .current').text();
				if (inputs[i].checked){

					obj.RESTRICTIONS.push(inputs[i].value) ; 
				}else{


				}
			}
					
		// CHECK BOXES

			if (inputs[i].classList.contains('tf-checkbox') ){
			//obj[inputs[i].name] = $('.box-fieldset .nice-select .current').text();
				if (inputs[i].checked){
					obj[inputs[i].name] = true;
					obj.OPTIONS.push(inputs[i].name) ; 

				}else{
					obj[inputs[i].name] = false;

				}
			}
			
		// FILES

			if (inputs[i].classList.contains('ip-file')){
				var FAKE_PATH = inputs[i].value;
				
				FILE_NAME = FAKE_PATH.split("\\").pop();
				 if (inputs[i].name != "" ){ 
					obj[inputs[i].name] = '../images/propiedades/' + FILE_NAME;  
				 }
			}
		
	} 
	
	
	var OBJ_DROPDOWNS = getSelectedOptionValue();
	
	obj = $.extend( obj, OBJ_DROPDOWNS );
	//obj = $.extend( obj, objRESTRICTIONS );
	
	debugger;
	
	var json = JSON.stringify(obj);
	console.log(json);
	return json;
	
	
}

function getSelectedOptionValue() {
    var selectedOptionValue = '';
	var OBJ = {};

    $('.nice-select').each(function() {
        var selectedValue = $(this).find('.current').text();
		var name = $(this).attr('name');
		OBJ[name] = selectedValue;
		//debugger;
        //console.log('Selected option value: ', selectedValue);
    });
    return OBJ;
}

function LOAD_FORM_DATA(id, tabla){
	
	
	//$("#TBL_IMAGENES_BDY").empty();
	//$("#TBL_BROCHURE_BDY").empty();
	//$("#TBL_UNITS_BDY").empty();
	//$('input:not(#' + 'TXT_DEV_ID' + ')').val('');
	//$('textarea').val('');
	
	//$("#CHK_SHOW").prop('checked', false);
	
    // DATOS
	
	var OBJ = {};
	

	OBJ['Datos'] = JSON.stringify(OBJ);
	
    OBJ['R'] = 'DEV_ID_2_JSON';
	OBJ['tabla'] = tabla; 
	OBJ['id'] = id;
	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    

	var RESP = POST(URL, datos);
	
	debugger; 
	
    // FRASEO RESPUESTA
	if (typeof RESP !== 'undefined' && typeof RESP.PROMPT !== 'undefined') {
		SHOW_ALERT(RESP.PROMPT,'DIV_ALERT'); return;
	}


	// TXT_ID
	
	if (typeof RESP.DATOS[0].ID !== 'undefined') {
		$('.X_ID').val( RESP.DATOS[0].ID);
	}
	
	$(".AVATAR_PIC").attr("src",RESP.DATOS[0].AVATAR_PIC); 
	
	
	// LLENA TEXTOS
	if (typeof RESP !== 'undefined' && typeof RESP.DATOS[0] !== 'undefined') {
		$.each(RESP.DATOS[0], function( key, value ) {
				
				
				$('[name="' + key + '"]').val('').val(value);
				

				
				
		});
	} 
	
	// LLENA DROPDOWNS
	
	$.each(RESP.DATOS[0].OPTIONS, function( key, OPTION ) {
		var T = OPTION;
		
		for (var OP in OPTION){
					//let ddSelect1 = $('[name="' + OP + '"]').msDropdown;
					let ddSelect = document.getElementById(OP).msDropdown;
					ddSelect.selectedIndex = 0;
					
					var O = ddSelect.options;
					for (var X in O){
						//console.log(O[X].value, X);
						if (O[X].value ==  OPTION[OP]) { ddSelect.selectedIndex = X;}else{ }
					} 
		}
	});

	var x = document.getElementById('FRM_EVENT');
    x.style.display = "block";
	
	$('html, body').animate({scrollTop: $('#' + el).offset().top}, 5);
	
}

$(document).on('click',".BTN_RESET_EVENT", function BTN_RESET_PROPERTY(){

	$("#FRM_EVENT input").val("");
});

function GENERA_LISTA(EVENT_ID) {

	var OBJ = {};
	

	//OBJ['Datos'] = JSON.stringify(OBJ);
	
    OBJ['R'] = 'GENERA_LISTA'; 
	OBJ['EVENT_ID'] = EVENT_ID;
	
	//console.log(OBJ);
	
	debugger;
		
	POST_API_v2(OBJ, 'API/create_excel/', true)
	.then(response => {
			// Handle the successful response here
			console.log("Response from server:", response);
			window.location.href = response.PATH ;
			//return response;
		})
		.catch(error => {
			// Handle the error here
			console.error("Error during API call:", error);
		});
		
    // VERIFICACIONES
	
	/*
    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../ADMIN/PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    
 
	debugger; 
	var RESP = POST(URL, datos);
	
	//debugger; 
	
    // FRASEO RESPUESTA
	if (typeof RESP !== 'undefined' && typeof RESP.PROMPT !== 'undefined') {
		SHOW_ALERT(RESP.PROMPT,'DIV_ALERT'); return;
	}
		//debugger; 
    //e.preventDefault();  //stop the browser from following
	
	if (typeof RESP !== 'undefined' && typeof RESP.PATH !== 'undefined') {
		window.location.href = RESP.PATH ;
	}
	*/



};

$(document).on('click', ".radio_m", function RADIO(el){
	
	RADIO_SELECT(this);
	
});

$(document).on('click', ".SEL_DISH", function RADIO(el){
	
	debugger;
	
	var FOR_RADIO = $(this).attr('for');
	
	
	$('#' + FOR_RADIO).click() ;
	RADIO_SELECT(this);
	//document.getElementById('#' + FOR_RADIO).checked = true;
	
});


function RADIO_SELECT(sender) {
	
	//console.log (this);
	//console.log (el.parent().text());
	//var P = sender.attributes[2].value;
	var PERSONA = $(sender).attr('persona');
	var TIEMPO  = $(sender).attr('tiempo');
	
	
	$('#PERSONA[persona="' + PERSONA + '"]').attr('saved', 'false');

	debugger;
	
	//$('#T2_HEADER').text('  - ' + $('input[name="T2"]:checked').parent().text() + '✅');
	//$('#T3_HEADER').text('  - ' + $('input[name="T3"]:checked').parent().text() + '✅');
	//$('#T4_HEADER').text('  - ' + $('input[name="T4"]:checked').parent().text() + '✅');
	
	$('#T1_HEADER[persona="' + PERSONA + '"]').text('Seleccionar ⚠️');
	$('#T2_HEADER[persona="' + PERSONA + '"]').text('Seleccionar ⚠️');
	$('#T3_HEADER[persona="' + PERSONA + '"]').text('Seleccionar ⚠️');
	$('#T4_HEADER[persona="' + PERSONA + '"]').text('Seleccionar ⚠️');
	
	$('#ICO_P' + PERSONA + '_T1').removeClass('LLENO').addClass('VACIO');
	$('#ICO_P' + PERSONA + '_T2').removeClass('LLENO').addClass('VACIO');
	$('#ICO_P' + PERSONA + '_T3').removeClass('LLENO').addClass('VACIO');
	$('#ICO_P' + PERSONA + '_T4').removeClass('LLENO').addClass('VACIO');
	
	if ( $('input[name="T1"][persona="' + PERSONA + '"]:checked').length > 0 ) { $('#T1_HEADER[persona="' + PERSONA + '"]').text('   ' + $('input[name="T1"][persona="' + PERSONA + '"]:checked').parent().text() + '' ); $('#ICO_P' + PERSONA + '_T1').removeClass('VACIO').addClass('LLENO'); }
	if ( $('input[name="T2"][persona="' + PERSONA + '"]:checked').length > 0 ) { $('#T2_HEADER[persona="' + PERSONA + '"]').text('   ' + $('input[name="T2"][persona="' + PERSONA + '"]:checked').parent().text() + ''); $('#ICO_P' + PERSONA + '_T2').removeClass('VACIO').addClass('LLENO');}
	if ( $('input[name="T3"][persona="' + PERSONA + '"]:checked').length > 0 ) { $('#T3_HEADER[persona="' + PERSONA + '"]').text('   ' + $('input[name="T3"][persona="' + PERSONA + '"]:checked').parent().text() + ''); $('#ICO_P' + PERSONA + '_T3').removeClass('VACIO').addClass('LLENO'); }
	if ( $('input[name="T4"][persona="' + PERSONA + '"]:checked').length > 0 ) { $('#T4_HEADER[persona="' + PERSONA + '"]').text('   ' + $('input[name="T4"][persona="' + PERSONA + '"]:checked').parent().text() + '');$('#ICO_P' + PERSONA + '_T4').removeClass('VACIO').addClass('LLENO'); }

	//debugger;
	//$('.BTN_SEND_SELECTION').prop('disabled',true).addClass('disabled');

	if ($('input[name="T1"][persona="' + PERSONA + '"]:checked').length > 0 && $('input[name="T2"][persona="' + PERSONA + '"]:checked').length > 0 && $('input[name="T3"][persona="' + PERSONA + '"]:checked').length > 0 && $('input[name="T4"][persona="' + PERSONA + '"]:checked').length > 0 ) {
	
		
		//$('.BTN_SEND_SELECTION').removeClass('disabled');
		//$('.BTN_SEND_SELECTION').removeProp('disabled');
		//$('.BTN_SEND_SELECTION').prop('disabled',false);
		
		//debugger;
	}else{
	
		//$('.BTN_SEND_SELECTION').prop('disabled',true).addClass('disabled');
	  //$('.BTN_SEND_SELECTION').prop('disabled',true).addClass('disabled');
		//debugger;
	}
	
	//if ( $('input[name="T1"]:checked') && $('input[name="T2"]:checked')  && $('input[name="T3"]:checked')  && $('input[name="T4"]:checked')   ){ $('.BTN_SEND_SELECTION').show(); }
	
	//debugger;
	TIEMPO =  parseInt(TIEMPO);
	
	if ( TIEMPO < 4 && TIEMPO > 0 ) {
	
		//TIEMPO = TIEMPO +1;
		TIEMPO = TIEMPO + 0;
		$('#T' + TIEMPO + '_HEADER[persona="' + PERSONA + '"]').click();
		//SCROLL_TO_ID('#PANEL_HEADING[persona="' + PERSONA + '"]');
		
	  
	}
	
	if ( TIEMPO == 4  ) {
	
	  $('#T4_HEADER[persona="' + PERSONA + '"]').click();
	
	}
	
	
	ACTUALIZAR_BTN_SEND_NUMERO();

	
}




function SCROLL_TO_ID(ID){
		//$(ID).scrollTo("500px" ); 
		
		//$(ID).get(0).scrollIntoView({behavior: 'smooth', block: 'center'});
		$('html,body').animate({scrollTop: $(ID).offset().top }, 50);
		//debugger;
		
	  //$([document.documentElement, document.body]).animate({			scrollTop: $(ID).offset().top		}, 1000);
}


$(document).on('click','.panel-heading', function HEADER_CLICK(e) {
	
	var PERSONA = $(this).attr('persona');
	//var TIEMPO  = $(this).attr('tiempo');
	
	//SCROLL_TO_ID('#PANEL_HEADING[persona="' + PERSONA + '"]');


	//debugger;
	


}); 


$(document).on('click', '.DELETE_PERSONA', function () {
   
	var PERSONA = $(this).attr('persona');
	var P_ID 	= $(this).attr('p_id');
	
	$('#PERSONA[persona="' + PERSONA + '"]').remove();
	
	debugger;
	
	var OBJ = {};
	
    OBJ['R'] 			= 'DELETE_PERSONA'; 
	OBJ['P_ID']			= P_ID;
	

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    
	var RESP = POST(URL, datos);
		
    // FRASEO RESPUESTA
	if (typeof RESP !== 'undefined' && typeof RESP.PROMPT !== 'undefined') {
		
		POPUP_INFO(RESP.PROMPT,  'OK');
		ACTUALIZAR_BTN_SEND_NUMERO();
		return;
	}
	
	 ACTUALIZAR_BTN_SEND_NUMERO();
	
	//debugger;
	   
});

$(document).on('click','.CHK_OPTION', function OPTION(e) {
	
	var PERSONA = $(this).attr('persona');
	var TIEMPO = $(this).attr('tiempo');
	
	debugger;
	//$('.BTN_SEND_SELECTION').addClass('disabled');
	
	
	if (this.checked) {

	var RESTRICCION = this.value;
	

		 
		 $('input[type="radio"][persona="' + PERSONA + '"]').not('.' + this.value).prop('disabled', true).prop('checked', false);
		 $('input[type="radio"][persona="' + PERSONA + '"]').not('.' + this.value).prop('checked', false);
		 $('button[persona="' + PERSONA + '"]').not('.' + this.value).prop('disabled', true).css('backgroundColor', 'rgba(var(--color-rgb), 0.5)');
		debugger;
		 

		
	}else{
		
		$('input[type="radio"][persona="' + PERSONA + '"]').not('.' + this.value).prop('disabled', false);	
		
		$('button[persona="' + PERSONA + '"]').not('.' + this.value).prop('disabled', false).css('backgroundColor', 'rgba(var(--color-rgb), 1)');
		
		debugger;


	}
	//debugger;
	$('#RESTRICCIONES_HEADER[persona="' + PERSONA + '"]').text( $('input[type="checkbox"]:checked[persona="' + PERSONA + '"]').length );
	
	RADIO_SELECT(this);


}); 

$(document).on('click','#BTN_AGREGAR_PERSONA', function BTN_AGREGAR_PERSONA(e) {
	
	e.preventDefault();
	


	POPUP_INPUT_NAME('Ingrese sus datos para seleccionar sus platillos.', 'Nombre y Apellido', 'Introduzca un nombre y apellido válido', 'Agregar');

	

	
	
}); 

function AGREGAR_PERSONA(nombre){
	
	nombre = nombre.split(' ').map(function(word) {
				return word.charAt(0).toUpperCase() + word.slice(1);
			}).join(' ');

					
	var OBJ = {};
	
	var count = $('div#PERSONA').length;
	
	//debugger;
	
    OBJ['R'] 		= 'BTN_AGREGAR_PERSONA'; 
	//OBJ['EVENT_ID'] = event_id;
	OBJ['NOMBRE']	= nombre;
	OBJ['INDEX']	= count + 1;
	
	//console.log(OBJ);
		
    // VERIFICACIONES

    // LLAMADA
    var OBJ_STR = JSON.stringify(OBJ);
    var URL = '../PHP/ajax.php';
    var datos = `${OBJ_STR}`;
    
 
	//debugger; 
	var RESP = POST(URL, datos);
	
	//debugger; 
	
	
	
    // FRASEO RESPUESTA
	if (typeof RESP !== 'undefined' && typeof RESP.PROMPT !== 'undefined') {
		
		POPUP_INFO(RESP.PROMPT,  'OK');
		return;
	}

	
	
	
	ACTUALIZAR_BTN_SEND_NUMERO();

	return RESP.HTML;
	
	
}

function ACTUALIZAR_BTN_SEND_NUMERO() {
	
	var forms = $('form').length;
	//var saved = $('' '.saved').length;
	var saved = $('#PERSONA[saved="true"]').length;
	
	debugger;
	var TO_SAVE = forms - saved;
	
	
	if ( TO_SAVE == 0 ){ $('#BTN_SEND_SELECTION').text('Enviar' + ''); }
	if ( TO_SAVE != 0 ){ $('#BTN_SEND_SELECTION').text('Enviar' + ' (' + TO_SAVE  + ')'); }
		
	if ( TO_SAVE == 0 ){ $('#BTN_SEND_SELECTION').css('background-color', 'rgba(var(--color-rgb), 0.5)'); }
	if ( TO_SAVE != 0 ){ $('#BTN_SEND_SELECTION').css('background-color', 'rgba(var(--color-rgb), 1)'); }
			
	if ( TO_SAVE == 0 ){ $('#BTN_SEND_SELECTION').prop('disabled', true); }
	if ( TO_SAVE != 0 ){ $('#BTN_SEND_SELECTION').prop('disabled', false);  }
	
	
	
	
	
}

function POPUP_INPUT_NAME(TEXTO, PLACEHOLDER_TXT, ERR_TXT, BTN_TXT){
	
	
  var R_NAME = $.confirm({
	  title: '',
	  animation: 'right',
	  animationSpeed: 1500,
	  content: '' +
	  '<form action="" class="formName">' +
	  '<div class="form-group">' +
	  '<label>' + TEXTO + '</label>' +
	  '<input type="text" placeholder="' + PLACEHOLDER_TXT + '" class="name form-control" required />' +
	  '</div>' +
	  '</form>',
	  theme: 'my-theme2',
	  buttons: {
		  formSubmit: {
			  text: BTN_TXT,
			  btnClass: 'btn-blue',
			  action: function () {
			  
				  var name = this.$content.find('.name').val();
				  
					var forms = $('form');
					
					var FRM_NOMBRES = [];
					
					forms.each(function(index, form) {
					
					  var NOMBRE = $(form).attr('nombre');
						if ( typeof NOMBRE !== "undefined" ){
						  FRM_NOMBRES.push(NOMBRE.trim().toLowerCase());
						}
					  
					});  
					
					//debugger;
					  
					if(jQuery.inArray(name.trim().toLowerCase(), FRM_NOMBRES) != -1) {
					
							  $.alert('Nombre duplicado.');
							  return false;
					}

				  // Validate that the name contains at least 2 words
				  
				  name = $.trim(name);
				  
				  if (name.split(/\s+/).length < 2) {
				  
					  $.alert(ERR_TXT);
					  return false;
					
				  } else {
				  
				  
				  
						name = name.split(' ').map(function(word) {
									return word.charAt(0).toUpperCase() + word.slice(1);
								}).join(' ');
								
						var NUEVA_PERSONA_HTML = AGREGAR_PERSONA(name);
						
						$('#PERSONAS').append(NUEVA_PERSONA_HTML);

						//$('.BTN_SEND_SELECTION').prop('disabled',true).addClass('disabled');
						
						$('html, body').animate({ scrollTop: $(document).height() }, 1000);
												
					  return name;

				  }
				  
				  //return false;
			  }
		  },
		  cancel: function () {
			  //close
		  },
	  },
	  onContentReady: function () {
		  // bind to events
		  var jc = this;
		  this.$content.find('form').on('submit', function (e) {
			  // if the user submits the form by pressing enter in the field.
			  e.preventDefault();
			  jc.$$formSubmit.trigger('click'); // reference the button and click it
		  });
	  }
	  
	  
  });
  
  //debugger;
	
}



$(document).on('click','.BTN_SALIR', function BTN_SALIR() {
	
	
	
	var UN_SAVED = $('#PERSONA[saved="false"]').length;
	debugger;
	if (UN_SAVED > 0 ){
		$.confirm({
			title: '',
			content: 'Esta seguro que desea salir sin guardar los cambios?',
			buttons: {
				SI: function () {
					//$.alert('Confirmed!');
					
					EXTERNAL_SALIR();
					
					
				},
				cancel: {
					text: 'NO',
					action: function () {
					
						//$.alert('Canceled!');
						
						//return false;
					}
				}
			}
		});
	
	}else{
	
		EXTERNAL_SALIR();
	
	}
	
	
}); 

function EXTERNAL_SALIR(){
	
	
		var OBJ = {};

		//debugger;
		
		OBJ['R'] 		= 'BTN_SALIR'; 
		
		//console.log(OBJ);
			
		// VERIFICACIONES

		// LLAMADA
		var OBJ_STR = JSON.stringify(OBJ);
		var URL = '../PHP/ajax.php';
		var datos = `${OBJ_STR}`;
		
	 
		var RESP = POST(URL, datos);
		//debugger; 
		
		window.location.href = '../' + RESP.E_ID;
		
		
		
		debugger; 
	
	
}

function DELETE_PREVIOUS(){
	
	
	var OBJ = {};
	

	
	OBJ['R'] 			= 'DELETE_PREVIOUS';

	
	// LLAMADA
	var OBJ_STR = JSON.stringify(OBJ);
	var URL = '../PHP/ajax.php';
	var datos = `${OBJ_STR}`;

	var RESP = POST(URL, datos);
	
	//$('.panel-heading').removeClass('saved');
	//$('.panel-heading').css('background-color', '#f5f5f5');
	
}


$(document).on('click', ".BTN_SEND_SELECTION", function BTN_SEND_SELECTION(e) {
	
	//$('.BTN_SEND_SELECTION').hide();
	//$('#BTN_SEND_SELECTION').hide();
	
	e.preventDefault();
	
	
	DELETE_PREVIOUS();
	
	var forms = $('form');
	
	//POPUP_INFO('Guardando...', 'OK');
	
	var FORMAS_OBJ = {};
	
	forms.each(function(index, form) {
	
			  var OBJ = {};
			  
			  //console.log('Form ' + index + ':', form);

			  var PERSONA = $(form).attr('persona');
			  var NOMBRE = $(form).attr('nombre');
			
			  var form_id = form.id;
			  
			  OBJ['Datos'] 		= FORM_2_OBJ(form_id);
			  OBJ['R'] 			= 'BTN_SEND_SELECTION';
			  OBJ['NOMBRE'] 	= NOMBRE;
			  OBJ['EMAIL'] 		= $('#USER_NAME').attr('user');
			  OBJ['E_ID'] 		= $('#E_ID').attr('e_id');
			  
			  
			  //console.log(OBJ);
			  
			  //debugger;
				  
			  // VERIFICACIONES  
		   
			  // LLAMADA
			  var OBJ_STR = JSON.stringify(OBJ);
			  var URL = '../PHP/ajax.php';
			  var datos = `${OBJ_STR}`;

			  var RESP = POST(URL, datos);
			  
			  FORMAS_OBJ[index] = OBJ;
			  
			  //debugger;
			  
			  $('#PERSONA[persona="' + PERSONA + '"]').each(function() {
				$(this).attr('saved', 'true');
			  });
			  

				$('form[persona="' + PERSONA + '"]').each(function() {

					var panelHeadings = $(this).find('.panel-heading');

						//panelHeadings.attr('saved', 'true');

					if (panelHeadings.length === $(this).children().length) {

					}
				});
			  
	  
	});
	
	POPUP_INFO('Selecciones enviadas al correo: <br><br>' + $('#USER_NAME').attr('user') + ' <br><br>Por favor verifique su bandeja de spam', 'OK');
	
	//$('.BTN_SEND_SELECTION').show();
	
	COLLAPSE_ALL();
	
	EMAIL_SELECTIONS(FORMAS_OBJ);
	
	//console.log(FORMAS_OBJ);
	
	debugger;
	

	
});



function POPUP_INPUT(TEXTO, PLACEHOLDER_TXT, ERR_TXT, BTN_TXT){
	
	
  $.confirm({
	  title: '',
	  content: '' +
	  '<form action="" class="formName">' +
	  '<div class="form-group">' +
	  '<label>' + TEXTO + '</label>' +
	  '<input type="text" placeholder="' + PLACEHOLDER_TXT + '" class="name form-control" required />' +
	  '</div>' +
	  '</form>',
	  theme: 'modern',
	  buttons: {
		  formSubmit: {
			  text: BTN_TXT,
			  btnClass: 'btn-blue',
			  action: function () {
				  var name = this.$content.find('.name').val();
				  if(!name){
					  $.alert(ERR_TXT);
					  return false;
				  }
				  //$.alert('Your name is ' + name);
				  
				  return name;
			  }
		  },
		  cancel: function () {
			  //close
		  },
	  },
	  onContentReady: function () {
		  // bind to events
		  var jc = this;
		  this.$content.find('form').on('submit', function (e) {
			  // if the user submits the form by pressing enter in the field.
			  e.preventDefault();
			  jc.$$formSubmit.trigger('click'); // reference the button and click it
		  });
	  }
  });
	
}

function POPUP_INFO(TEXTO,  BTN_TXT){
	

  $.confirm({
	  title: '',
	  content: '' +
	  '<form action="" class="formName">' +
	  '<div class="form-group">' +
	  '<label>' + TEXTO + '</label>' +

	  '</div>' +
	  '</form>',
	  theme: 'my-theme2',
	  buttons: {
		  formSubmit: {
			  text: BTN_TXT,
			  btnClass: 'btn-blue',
			  action: function () {

				  //$.alert('Your name is ' + name);
				  
				  return;
			  }
		  },

	  },
	  onContentReady: function () {
		  // bind to events
		  var jc = this;
		  this.$content.find('form').on('submit', function (e) {
			  // if the user submits the form by pressing enter in the field.
			  e.preventDefault();
			  jc.$$formSubmit.trigger('click'); // reference the button and click it
		  });
	  }
  });
	


	
}

$(document).on('click', "img", function BTN_SEND_SELECTION(e) {
	
  //debugger;
  //this.requestFullscreen();
  
  const imgs = document.querySelectorAll('img');
  const fullPage = document.querySelector('#fullpage');

  imgs.forEach(img => {
	img.addEventListener('click', function() {
	  fullPage.style.backgroundImage = 'url(' + img.src + ')';
	  fullPage.style.display = 'block';
	});
  });

});


function LOGIN(){
	
  var HTML_FORM =  `
	  <form id="formLogin" action="" class="formLogin"> 
		<div class="form-group">
		  <label>Nombre</label> 
		  <input type="text" placeholder="Nombre" class="name form-control" name="TXT_LOGIN_USER" required /> 
		  <label>Email</label> 
		  <input type="text" placeholder="Email" class="email form-control" name="email" required /> 
		</div>
	  </form>`;
		
   HTML_FORM =  `
   
		

			
			  <form id="formLogin" action="" class="formLogin"> 
				<div class="form-group">
					
					<label class="TITULO_MY_THEME">¡Bienvenido!</label> 
					<br><br>
					<label class="SUBTITULO_MY_THEME">Ingresa y selecciona los platillos que más te gustan para hacer de tu evento una experiencia deliciosa e inolvidable.</label> 
					<br><br>
					
				  <input type="text" placeholder="Email" class="email form-control" name="email" required /> 
				</div>
			  </form>
			
		`;
	
  $.confirm({
	  title: ' ',
	  animation: 'right',
	  animationSpeed: 1500,
	  content: HTML_FORM,
	  theme: 'my-theme2',

	  buttons: {
		  formSubmit: {
			  text: 'Ingresar',
			  btnClass: '',
			  action:  function () {
					
					
				  var name = this.$content.find('.name').val();
				  var email = this.$content.find('.email').val();
				  
				  
				  //if(!name){ $.alert('Nombre no valido');  return false;  }
				  //if(valitadeName(name) == false){  POPUP_INFO('Ingrese nombre y apellido válidos.',  'OK'); return false;   }
				  if(!email){ $.alert('Correo no valido');  return false;  }
				  if (containsEmail(email) == false){ $.alert('Correo no valido');  return false;  }
				  
				  
				  
				  let formLogin = document.getElementById('formLogin');
				  let fd = new FormData(formLogin);
				  
				  fd.append('R', 'BTN_LOGIN_GUESTS_V2');
				  
				 
				  var data = Object.fromEntries(fd);

				  //debugger; 

				  var RE;
				  
				  fetch('../PHP/ajax.php',{
					method: 'POST',
					headers: {'Content-Type': 'application/json'},
					body: JSON.stringify(data)
				  
				  }).then(res => res.json())
					.then(response => EXTERNAL(response))
					.catch(error => console.log(error)); 
					
				  //console.log( "RESP:", await R);
				  

					
			  }
		  }
		  
	  },
	  onContentReady: function () {
		  // bind to events
		  var jc = this;
		  this.$content.find('form').on('submit', function (e) {
			  // if the user submits the form by pressing enter in the field.
			  e.preventDefault();
			  jc.$$formSubmit.trigger('click'); // reference the button and click it
		  });
	  }
  });
	
	
}


function containsEmail(str) {
        // Regular expression for validating an email address
        var emailPattern = /[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/;

        // Test the string against the regex pattern
        return emailPattern.test(str);
    }
	
	
	
function EXTERNAL(response){
	
	
	//$.alert(response.PROMPT);
	console.log(response);
	
	debugger;
	
	if (response.USUARIO_NUEVO == true) { }
	
	var PERSONAS = $('div#PERSONA').length;
	
	//debugger; 
	//if ( PERSONAS == 0 ) { POPUP_INPUT_NAME('Agregar comensal', 'Nombre y Apellido', 'Introduzca un nombre y apellido válido', 'Agregar'); }

	location.reload(); 
	
	
}


function valitadeName(name){
	
	
      debugger;
	const inputField = name;
	const inputValue = inputField.trim();
	const wordCount = inputValue.split(/\s+/).filter(Boolean).length; // Split by whitespace and filter out empty strings

	if (wordCount < 2) {
		//alert('Please enter at least two words.');
		return false; // Prevent form submission
	}
	return true; // Allow form submission
        
	
}


function COLLAPSE_ALL() {
	
	
	var forms = $('form');
	
	forms.each(function(index, form) {
		
		var PERSONA = $(form).attr('persona');
		var NOMBRE = $(form).attr('nombre');
		

		
		$('.panel-collapse').each(function() {
			$(this).removeClass('in');
		});
						

						
	}); 
	
}


function EMAIL_SELECTIONS(FORMAS_OBJ){
	
	debugger;
	
	
	var data = FORMAS_OBJ; 


	
	fetch('../API/Email.php',{
			method: 'POST',
			headers: {'Content-Type': 'application/json'},
			body: JSON.stringify(data)
		}).then(res => res.json())
		.then(response => console.log('API/EMAIL' , response))
		.then(AFTER_EMAIL())
		.catch(error => console.log('API/EMAIL - ERROR - ' , error)); 
	
	
	
	
}

function AFTER_EMAIL(){
	
	
				var forms = $('form');
				
				forms.each(function(index, form) {
					
					var PERSONA = $(form).attr('persona');
					var NOMBRE = $(form).attr('nombre');
					
					$('#PERSONA[persona="' + PERSONA + '"]').attr('saved', 'true'); 		
			
				}); 
				
				ACTUALIZAR_BTN_SEND_NUMERO();
				
	
}


function POPUP_NUEVO_PLATILLO(TEXTO, PLACEHOLDER_TXT, ERR_TXT, BTN_TXT){
	
	
  var R_NAME = $.confirm({
	  title: '',
	  animation: 'right',
	  animationSpeed: 1500,
	  content: '' +
	  '<form action="" class="formName">' +
	  '<div class="form-group">' +
	  '<label>' + TEXTO + '</label>' +
	  '<input type="text" placeholder="' + PLACEHOLDER_TXT + '" class="name form-control" required />' +
	  '</div>' +
	  '</form>',
	  theme: 'my-theme2',
	  buttons: {
		  formSubmit: {
			  text: BTN_TXT,
			  btnClass: 'btn-blue',
			  action: function () {
			  
				  var name = this.$content.find('.name').val();
				  
					var forms = $('form');
					
					var FRM_NOMBRES = [];
					
					forms.each(function(index, form) {
					
					  var NOMBRE = $(form).attr('nombre');
						if ( typeof NOMBRE !== "undefined" ){
						  FRM_NOMBRES.push(NOMBRE.trim().toLowerCase());
						}
					  
					});  
					
					//debugger;
					  
					if(jQuery.inArray(name.trim().toLowerCase(), FRM_NOMBRES) != -1) {
					
							  $.alert('Nombre duplicado.');
							  return false;
					}

				  // Validate that the name contains at least 2 words
				  
				  name = $.trim(name);
				  
				  if (name.split(/\s+/).length < 2) {
				  
					  $.alert(ERR_TXT);
					  return false;
					
				  } else {
				  
				  
				  
						name = name.split(' ').map(function(word) {
									return word.charAt(0).toUpperCase() + word.slice(1);
								}).join(' ');
								
						var NUEVA_PERSONA_HTML = AGREGAR_PERSONA(name);
						
						$('#PERSONAS').append(NUEVA_PERSONA_HTML);

						//$('.BTN_SEND_SELECTION').prop('disabled',true).addClass('disabled');
						
						$('html, body').animate({ scrollTop: $(document).height() }, 1000);
												
					  return name;

				  }
				  
				  //return false;
			  }
		  },
		  cancel: function () {
			  //close
		  },
	  },
	  onContentReady: function () {
		  // bind to events
		  var jc = this;
		  this.$content.find('form').on('submit', function (e) {
			  // if the user submits the form by pressing enter in the field.
			  e.preventDefault();
			  jc.$$formSubmit.trigger('click'); // reference the button and click it
		  });
	  }
	  
	  
  });
  
  //debugger;
	
}


function formToJson(formId) {
    var data = {};
    var data_clean = {};
	//debugger;
    $('#' + formId).find('input, textarea, select, img, option, span').each(function() {
		//debugger;
        if (this.name && !this.disabled && (this.checked
                || /select|textarea|img/i.test(this.nodeName)
                || /text|hidden|password|file|checkbox/i.test(this.type))) {
            if(this.type === 'file'){
				data_clean[this.name] = this.value.replace(/C:\\fakepath\\/i, '');

            } else if (this.nodeName === 'IMG'){
				data_clean[this.name] = this.src;

            } else if(this.type === "checkbox"){
                data_clean[this.name] = this.checked ? $(this).val() : "off"

            } else if(this.nodeName === "span"){
                data_clean[this.name] = this.text();

				
            } else {
                data_clean[this.name] = $(this).val();

            }
        }
    });
    return JSON.stringify(data);
}

function formToJson_v2(formId) {
    var data_clean = {};

    $('#' + formId).find('input, textarea, select, img, label, span, table').each(function() {
        var element = $(this); // Use jQuery object for consistency
        var nodeName = this.nodeName;
        var type = this.type; // More reliable than element.attr('type') for inputs

        var nameAttribute = (nodeName === 'SPAN') ? element.data('name') : this.name;
        var tbsAttribute = element.attr('TBS');
        var attributeKey;

        // Determine the attributeKey based on the new rule for radio buttons
        if (nodeName === 'INPUT' && type === 'radio') {
            attributeKey = tbsAttribute; // For radio, key is ALWAYS TBS
        } else {
            attributeKey = nameAttribute ? nameAttribute : tbsAttribute; // Original logic for others
        }

        // Proceed only if an attributeKey is determined and the element is not disabled
        if (attributeKey && !this.disabled) {
            if (nodeName === 'LABEL') {
                data_clean[attributeKey] = this.innerText.trim();

            } else if (type === 'file' && nodeName === 'INPUT') { // Be more specific for file inputs
                data_clean[attributeKey] = this.value.replace(/C:\\fakepath\\/i, '');

            } else if (nodeName === 'IMG') {
                data_clean[attributeKey] = this.src;

            } else if (nodeName === 'SPAN') {
                data_clean[attributeKey] = this.innerText.trim();

            } else if (nodeName === 'TABLE') {
                var tableData = [];
                element.find('tr').each(function(rowIdx, row) {
                    var rowData = {};
                    $(row).find('td').each(function(cellIdx, cell) {
                        var input = $(cell).find('input'); // Check for inputs within table cells
                        if (input.length > 0) {
                            var subTbsAttribute = input.attr('SUB_TBS');
                            // Prefer SUB_TBS, but could fall back to input's name or TBS if needed
                            if (subTbsAttribute) {
                                rowData[subTbsAttribute] = input.val();
                            } else if (input.attr('name')) { // Fallback for cell inputs
                                rowData[input.attr('name')] = input.val();
                            }
                        } else {
                            // If no input, use generic cell key (or a data-key attribute on <td> if you prefer)
                            rowData['cell_' + cellIdx] = $(cell).text().trim();
                        }
                    });
                    if (!$.isEmptyObject(rowData)) {
                        tableData.push(rowData);
                    }
                });
                data_clean[attributeKey] = tableData;

            } else if (type === "checkbox" && nodeName === 'INPUT') {
                //data_clean[attributeKey] = this.checked ? element.val() : "off";
                data_clean[attributeKey] = this.checked ? true : false;

            } else if (nodeName === 'INPUT' && type === 'radio') {
                // Value for radio button is only set if it's checked
                if (this.checked) {
                    data_clean[attributeKey] = element.val().trim();
                }
                // If not checked, it's simply not added to data_clean under this key.
                // If multiple radios with the same TBS are checked (which shouldn't happen with same name),
                // the last one processed would win.
            } else {
                // Default for other input types (text, password, hidden, etc.), textarea, select
                var val = element.val();
                data_clean[attributeKey] = (typeof val === 'string') ? val.trim() : val;
            }
        }
    });

    return JSON.stringify(data_clean);
}

function jsonToForm(form, jsonData) {
    var data = jsonData;
    $.each(data, function(name, field) {
        var $input = $('#' + form).find('[name="'+name+'"]');
        switch(field.type) {
            case 'checkbox':
                $input.attr('checked', field.value === "off" ? false : true);
                break;
            case 'img':
                $input.attr('src', field.value);
                break;
            default:
                $input.val(field.value);
        }
    });
}

function jsonToForm_v2(formId, jsonData) {
	
    // Check if jsonData is a string and convert it to an object if necessary
    if (typeof jsonData === 'string') {
        try {
            jsonData = JSON.parse(jsonData); // Convert string to object
        } catch (e) {
            console.error("Invalid JSON string:", e);
            return; // Exit the function if parsing fails
        }
    }
	
    // Iterate through each key-value pair in the JSON object
    $.each(jsonData, function(name, value) {	
        // Find the form element by its name attribute
        var $element = $('#' + formId).find('[name="' + name + '"]');

        // Check if the element exists
        if ($element.length) {
            // Handle different types of form elements
            if ($element.is('input[type="date"]')) {
                // Specifically handle date inputs to ensure YYYY-MM-DD format
                if (typeof value === 'string' && value) { // Process non-empty strings
                    let datePart = value.substring(0, 10);
                    // Check if the extracted part matches YYYY-MM-DD format
                    if (/^\d{4}-\d{2}-\d{2}$/.test(datePart)) {
                        $element.val(datePart);
                    } else {
                        console.warn(`Value for date input [name="${name}"] ("${value}") could not be reliably converted to YYYY-MM-DD format. Attempting to set original value.`);
                        $element.val(value); // Fallback to original value
                    }
                } else {
                    // For null, undefined, empty string, or non-string types,
                    // let .val() handle it (e.g., .val(null) clears the input).
                    $element.val(value);
                }
            } else if ($element.is('input[type="text"]') || $element.is('input[type="hidden"]') || $element.is('textarea') || $element.is('input[type="number"]') || $element.is('input[type="email"]')  || $element.is('input[type="password"]') || $element.is('input[type="tel"]') || $element.is('input[type="time"]') ) {
                // For text, hidden, textarea, number, email, password, tel, and time inputs, set the value
                $element.val(value);
            } else if ($element.is('select')) {
                // For select elements, set the selected value
                $element.val(value).change();
            } else if ($element.is('input[type="checkbox"]')) {
                // For checkboxes, check or uncheck based on the value
                $element.prop('checked', value === 'true' || value === '1' || value === 'on');
            } else if ($element.is('input[type="radio"]')) {
                // For radio buttons, check the one that matches the value
                $element.filter('[value="' + value + '"]').prop('checked', true);
            } else if ($element.is('img')) {
                // For images, set the src attribute
                $element.attr('src', value);
            } else if ($element.is('label')) {
                // For images, set the src attribute
                $element.attr('text', value);
            } else if ($element.is('span')) {
                // For images, set the src attribute
                $element.html(value);
            } else if ($element.is('a')) {
                // For images, set the src attribute
                $element.prop('href', value);
            }
        }
    });
}

function jsonToForm_v3(formId, jsonData) { // Specific for Flight Search Modal
    if (!jsonData || typeof jsonData !== 'object') {
        console.error('jsonToForm_v3: Invalid or empty jsonData', jsonData);
        return;
    }

    const form = document.getElementById(formId);
    if (!form) {
        console.error('jsonToForm_v3: Form not found with ID:', formId);
        return;
    }

    // Iterate through each key-value pair in the JSON object
    $.each(jsonData, function(key, value) {
        var $element = $('#' + formId).find('[name="' + key + '"]');

        if (key === 'schedule' && typeof value === 'object' && value !== null) {
            // Handle nested schedule object
            $.each(value, function(scheduleKey, scheduleValue) {
                var fieldName = `schedule_${scheduleKey}`; // e.g., schedule_type, schedule_frequency_unit
                var $scheduleElement = $('#' + formId).find('[name="' + fieldName + '"]');

                if (scheduleKey === 'times_of_day' && Array.isArray(scheduleValue)) {
                    // Handle schedule_times_of_day[] array
                    var $timeInputs = $('#' + formId).find('input[name="schedule_times_of_day[]"]');
                    $timeInputs.each(function(index, input) {
                        if (scheduleValue[index] !== undefined) {
                            $(input).val(scheduleValue[index]);
                        } else {
                            $(input).val(''); // Clear if no corresponding time
                        }
                    });
                } else if ($scheduleElement.length) {
                    if ($scheduleElement.is('select')) {
                        // If the option doesn't exist, add it
                        if (scheduleValue !== null && scheduleValue !== undefined && String(scheduleValue).trim() !== '') {
                            if ($scheduleElement.find('option[value="' + scheduleValue + '"]').length === 0) {
                                var newOption = $('<option></option>')
                                                    .val(scheduleValue)
                                                    .html(scheduleValue); // Use value as text, or provide a more descriptive text if available
                                $scheduleElement.append(newOption);
                            }
                            $scheduleElement.val(scheduleValue).trigger('change');
                        } else {
                            $scheduleElement.val('').trigger('change'); // Or set to a default placeholder
                        }
                    } else if ($scheduleElement.is('input[type="checkbox"]')) {
                        $scheduleElement.prop('checked', scheduleValue === true || scheduleValue === 'true' || scheduleValue === 1 || scheduleValue === '1' || scheduleValue === 'on');
                    } else if ($scheduleElement.is('input[type="radio"]')) {
                        $scheduleElement.filter('[value="' + scheduleValue + '"]').prop('checked', true);
                    } else {
                        $scheduleElement.val(scheduleValue);
                    }
                }
            });
        } else if ($element.length) {
            // Handle different types of top-level form elements
            if ($element.is('select')) {
                // If the option doesn't exist, add it
                if (value !== null && value !== undefined && String(value).trim() !== '') {
                    if ($element.find('option[value="' + value + '"]').length === 0) {
                        var newOption = $('<option></option>')
                                            .val(value)
                                            .html(value); // Use value as text
                        $element.append(newOption);
                    }
                    $element.val(value).trigger('change');
                } else {
                    $element.val('').trigger('change'); // Or set to a default placeholder
                }
            } else if ($element.is('input[type="checkbox"]')) {
                $element.prop('checked', value === true || value === 'true' || value === 1 || value === '1' || value === 'on');
            } else if ($element.is('input[type="radio"]')) {
                $element.filter('[value="' + value + '"]').prop('checked', true);
            } else { // Handles text, number, date, time, hidden etc.
                $element.val(value);
            }
        }
    });

    // Ensure Select2 airport dropdowns are specifically updated if they weren't caught by name
    $('#' + formId).find('select.searchable-airport-select').each(function() {
        const $selectElement = $(this);
        const name = $selectElement.attr('name');
        if (jsonData.hasOwnProperty(name)) {
            const valueToSet = jsonData[name];
            // If the option doesn't exist, add it
            if (valueToSet !== null && valueToSet !== undefined && String(valueToSet).trim() !== '') {
                if ($selectElement.find('option[value="' + valueToSet + '"]').length === 0) {
                    var newOption = $('<option></option>')
                                        .val(valueToSet)
                                        .html(valueToSet); // Use value as text
                    $selectElement.append(newOption);
                }
                $selectElement.val(valueToSet).trigger('change');
            } else {
                $selectElement.val('').trigger('change'); // Or set to a default placeholder
            }
        }
    });
}

function clearForm(formId) {
    // Clear input fields
    $('#' + formId).find('input').each(function() {
        switch(this.type) {
            case 'password':
            case 'select-multiple':
            case 'select-one':
            case 'text':
            case 'email':
            case 'textarea':
            case 'date':
            case 'number':
            case 'tel':
            case 'url':
                $(this).val('');
                break;
            case 'checkbox':
            case 'radio':
                this.checked = false;
        }
    });

    // Clear textarea
    $('#' + formId).find('textarea').val('');

    // Clear select
    $('#' + formId).find('select').prop('selectedIndex', 0);

    // Clear img src
    $('#' + formId).find('img').attr('src', '');
}




function GET_AN_ID(tabla, DB_ID = 0) {
    var ID = null;
	
	var data = {table: tabla, DB_ID: DB_ID};
	
	
    $.ajax({
        url: './API/get_id/',
        type: 'GET',
        data: data,
        async: false,
        success: function(response) {
			debugger;
			response = JSON.parse(response);
            ID = response.ID;
        },
        error: function(error) {
			debugger;
            console.log(error);
        }
    });
    return ID;
}





$(document).on('click','.remove-row', function REMOVE_ROW(e) { 
	
	        // Hide the closest table row (<tr>) to the clicked button
        $(this).closest('tr').hide();
});


async function FN_NUEVO_USUARIO(e) { 
	
	debugger;
	
	var id = $('#FRM_NEW_USER input[TBS="EDIT_ID"]').val();
	
	if ( id == ''){
		var nuevo_id = GET_AN_ID('Users');
		$('#FRM_NEW_USER input[TBS="EDIT_ID"]').val(nuevo_id);
	}
	//$('input[TBS="EDIT_ID"]').val(data.ID);
	
	var saved = await ACT_BTN_GUARDAR(e);
	
	var saved_ok = saved.DATOS.QRY.OK;
	
	debugger;
	if (saved_ok === true) {	
		console.log('OK - Usuario guardado.');
	}else{
		console.log('ERROR - Usuario NO guardado.');
	
	}
	

}

function SAVE_CHANGES(e) {
    debugger;

    var FORM = $(e).attr('FRM');
    var DB = $(e).attr('DB');
    var DB_ID = $(e).attr('DB_ID');
	


    var id = $('#' + FORM + ' input[name="EDIT_ID"]').val();

    if (id == '' | id == null | id == undefined) {
        id = $('#' + FORM + ' input[TBS="EDIT_ID"]').val();
    }

    if (id == '' | id == null | id == undefined) {
        var nuevo_id = GET_AN_ID(DB, DB_ID);
        $('#' + FORM + ' input[TBS="EDIT_ID"]').val(nuevo_id);
        $('#' + FORM + ' input[TBS="EDIT_ID"]').trigger('change');
        $('#' + FORM + ' input[name="EDIT_ID"]').val(nuevo_id);
        $('#' + FORM + ' input[name="EDIT_ID"]').trigger('change');
    }

    // Call ACT_BTN_GUARDAR and wait for the promise to resolve
    return ACT_BTN_GUARDAR(e).then(saved => {
        // Handle the result of the save operation here if needed
        return saved; // Return the saved result if needed
    }).catch(error => {
        // Handle any errors that occurred during the save operation
        console.error("Error during save operation:", error);
    });
}

function ACT_BTN_GUARDAR(e) {
    var FORM = $(e).attr('FRM');
	var DB_ID = $(e).attr('DB_ID');
	var id = $('#' + FORM + ' input[name="EDIT_ID"]').val();

    var data_v2 = formToJson_v2(FORM);
    console.log(data_v2);

    var data_v3 = formToJson_v3(FORM);
    console.log(data_v3);

    const OBJ1 = {
        //ID: $('#' + FORM + ' input[TBS="EDIT_ID"]').val(),
        ID: id,
        id: id,
        TABLA: e.attributes.db.value,
        Datos: data_v2,
		DB_ID: DB_ID
    };

    debugger;

    // Return the promise from POST_API_v2
    return POST_API_v2(OBJ1, 'API/_DATA/json_merge/', true)
        .then(response => {
            // Handle the successful response here
            console.log("Response from server:", response);

            var saved_ok = response.DATOS.QRY.OK;

            var messages = 'Saved: ' + response.DATOS.QRY.OK + '</br>' + 'STRIPE: ' + '</br>' + response.STRIPE_MESSAGES.join('</br>');
            debugger;
            if (saved_ok === true) {
                console.log('OK');
                POP_UP_OK(messages);
            } else {
                POP_UP_NOK('Error.');
                console.log('ERROR');
            }
            debugger;

            return response; // Return the response for further handling
        })
        .catch(error => {
            // Handle the error here
            console.error("Error during API call:", error);
            throw error; // Rethrow the error for further handling
        });
}
//});


function LOAD_FORM(FORM,TABLA,ID) { 
	
	//e.preventDefault();
	
	//var FORM = $(this).attr('FRM');
	
	var OBJ = {}; 
	
	
    OBJ['R'] = 'JSON_LOAD';
	OBJ['tabla'] = this.attributes.db.value ;
	OBJ['id'] = this.attributes.id.value ;
		
    OBJ['R'] = 'JSON_LOAD';
	OBJ['tabla'] = TABLA ;
	OBJ['id'] = ID ;
	

	debugger;
	var RESP = POST_v2(datos);
	
    // FRASEO RESPUESTA
    var DATOS = RESP.DATOS;
	debugger;
	//$( "#B_PROFILE_CONTENT" ).empty();
	//$('#TOP_MENU').load('../PHP/HTML_TOP_MENU_2.php');
	//$('#B_PROFILE_CONTENT').load('../PHP/' + this.attributes.after.value + '.php');
	
	
	
	//jsonToForm(FORM, DATOS[0]);
	jsonToForm_v2(FORM, DATOS[0]);
	$('input[TBS="EDIT_ID"]').val(this.attributes.id.value);

	

	debugger;
	
	
	
	//location.reload();
	
	

};


// Function to hide the alert after a given time
function hideAlertAfterTime(alertElement, time) {
	setTimeout(function() {
		alertElement.fadeOut(1000); // Fade out effect over 500ms
	}, time);
}


// Function to hide the alert after a given time
function hideAlertAfterTime_v2(alertElement, time) {
	setTimeout(function() {
		$( alertElement ).remove();
	}, time);
}



// Optional: Hide the alert immediately when the remove button is clicked
$(document).on('click','.remove-button', function ALERT_FADE(e) { 
//$('.remove-button').on('click', function() {
	$('.alert').fadeOut(500);
});

function TOP_ALERT(TEXT){
	
var ALERT = '<div id="TOP_ALERT" class="alert alert-lilac bg-lilac-50 text-lilac-600 border-lilac-50 px-24 py-11 mb-0 fw-semibold text-lg radius-8 d-flex align-items-center justify-content-between" role="alert">' + TEXT + '<button class="remove-button text-lilac-600 text-xxl line-height-1"> <iconify-icon icon="iconamoon:sign-times-light" class="icon"></iconify-icon></button></div>';
	
	$( '#TOP_ALERT_DIV' ).append( ALERT);
	
	//jQuery(".alert").fadeOut("slow");
	
	
	
					
	hideAlertAfterTime_v2('#TOP_ALERT', 5000);
}


async function DELETE_ID(TABLA, ID, button) {
	
    //debugger;
	
    $.confirm({
		theme: 'modern',
        title: 'Delete',
        content: `
					<div>
						<p><img src="IMAGES/ALERTS/DELETE_GIF.gif"></p>
						<p>¿Está seguro de borrar el elemento?</p>
					</div>
				`,
        buttons: {
            confirm: function () {
                const row = $(button).closest("tr"); // Get the closest <tr> using the button reference
                row.addClass("d-none"); // Add the class d-none to hide the row


                const OBJ = {
                    ID: ID,
                    TABLA: TABLA,
                    DB_ID: DB_ID
                };
                 //POST_API(OBJ, '/API/delete_id/');
					POST_API_v2(OBJ, '/API/delete_id/', true)
					.then(response => {
							// Handle the successful response here
							console.log("Response from server:", response);
							//return response;
						})
						.catch(error => {
							// Handle the error here
							console.error("Error during API call:", error);
						});

            },
            cancel: function () {
                // $.alert('Canceled!');
                return;
            }
        }
    });
	
}

async function DELETE_STRIPE(TABLA, ID, button) {
	
    //debugger;
	
    $.confirm({
		theme: 'modern',
        title: 'Delete',
        content: `
					<div>
						<p><img src="IMAGES/ALERTS/DELETE_GIF.gif"></p>
						<p>¿Está seguro de borrar el elemento?</p>
					</div>
				`,
        buttons: {
            confirm: function () {

				
                const OBJ = {
                    id: ID,
                    TABLA: TABLA
                };
                 //POST_API(OBJ, '/API/delete_id/');
					POST_API_v2(OBJ, '/API/STRIPE/delete_id/', true)
					.then(response => {
							// Handle the successful response here
							debugger;
							
							
							console.log("Response from server:", response);
							
							if (response.success){
								if (response.message != ''){POP_UP_OK(response.message);}
								const row = $(button).closest("tr"); // Get the closest <tr> using the button reference
								row.addClass("d-none"); // Add the class d-none to hide the row
								//return response;
							}else{
								if (response.message != ''){POP_UP_NOK(response.message);}
							
							
							}
						})
						.catch(error => {
							// Handle the error here
							console.error("Error during API call:", error);
						});
				
            },
            cancel: function () {
                // $.alert('Canceled!');
                return;
            }
        }
    });
	
}


function POP_UP_OK(TEXTO) {
    return new Promise((resolve) => {
        $.confirm({
            theme: 'modern',
            title: '',
            content: `
                <div>
                    <p><img src="IMAGES/ALERTS/OK_GIF.gif"></p>
                    <p>${TEXTO}</p>
                </div>
            `,
            buttons: {
                confirm: function () {
                    resolve(); // Resolve the promise when the user clicks OK
                }
            }
        });
    });
}


function POP_UP_NOK(TEXTO) {
    return new Promise((resolve) => {
        $.confirm({
            theme: 'modern',
            title: '',
            content: `
                <div>
                    <p><img src="IMAGES/ALERTS/NOK_GIF.gif"></p>
                    <p>${TEXTO}</p>
                </div>
            `,
            buttons: {
                confirm: function () {
                    resolve(); // Resolve the promise when the user clicks OK
                }
            }
        });
    });
}


function CANCEL_SUBSCRIPTION(subscription_id) {
    $.confirm({
        theme: 'modern',
        title: 'Delete',
        content: `
            <div>
                <p><img src="IMAGES/ALERTS/DELETE_GIF.gif"></p>
                <p>¿Está seguro de cancelar?</p>
            </div>
        `,
        buttons: {
            confirm: function () {
                const OBJ = {};
                OBJ['SUBSCRIPTION_ID'] = subscription_id;

                // Call the POST_API_v2 function and handle the promise
                POST_API_v2(OBJ, '/API/STRIPE/cancel_subscription/', true)
                    .then(response => {
                        // Handle the successful response here
                        console.log("Response from server:", response);
                        if (typeof(response.DATOS.MESSAGE) !== "undefined" && response.DATOS.MESSAGE !== null) {
                            // Call POP_UP_OK and wait for it to resolve
                            return POP_UP_OK(response.DATOS.MESSAGE);
                        }
                    })
                    .then(() => {
                        // This will execute after POP_UP_OK resolves
                        location.reload();
                    })
                    .catch(error => {
                        // Handle the error here
                        console.error("Error during API call:", error);
                    });
            },
            cancel: function () {
                // User canceled the action
                return;
            }
        }
    });
}

function formToJson_v3(formId) {
	
	//console.log(form_struc_2_json(formId));
	
    var data_clean = {};
    
    $('#' + formId).find('input, textarea, select, img, label, span').each(function() {
        if (this.name && !this.disabled) {
            var value = getValueByNodeName(this); // Use auxiliary function to get the value

            // If the key already exists in data_clean
            if (!data_clean[this.name]) {
                // If it doesn't exist, create an object for it
                data_clean[this.name] = {};
            }

            // Create a property for the current input element
            var propertyName = getPropertyName(this); // Get the property name
			
            if (!data_clean[this.name][propertyName]) {
                data_clean[this.name][propertyName] = []; // Initialize as an array if it doesn't exist
            }
			
            //data_clean[this.name][propertyName].push(value); // Add the value to the array
			data_clean[this.name] = value; // Add the value to the array
        }
    });
    
    return JSON.stringify(data_clean);
}

// Auxiliary function to get the value based on nodeName
function getValueByNodeName(element) {
    switch (element.nodeName) {
        case 'LABEL':
            return element.innerText.trim(); // Handle label elements
        case 'IMG':
            return element.src; // Handle image elements
        case 'INPUT':
            if (element.type === 'file') {
                return element.value.replace(/C:\\fakepath\\/i, ''); // Handle file input
            } else if (element.type === 'checkbox') {
                return element.checked ? $(element).val() : "off"; // Handle checkbox input
            } else {
                return $(element).val(); // Handle other input types
            }
        case 'TEXTAREA':
        case 'SELECT':
            return $(element).val(); // Handle textarea and select elements
        default:
            return null; // Handle unsupported node types
    }
}

// Function to get property name based on nodeName
function getPropertyName(element) {
    switch (element.nodeName) {
        case 'LABEL':
            return 'text'; // For labels, store the text
        case 'IMG':
            return 'src'; // For images, store the source
        case 'INPUT':
            return element.type === 'checkbox' ? 'checked' : 'value'; // For checkboxes, store checked state, otherwise store value
        case 'TEXTAREA':
        case 'SELECT':
            return 'value'; // For textareas and selects, store the value
        default:
            return 'value'; // Default case
    }
}



function JsonToForm_v3(formId, jsonData) {
    var data = JSON.parse(jsonData); // Parse the JSON input

    $('#' + formId).find('input, textarea, select, img, label').each(function() {
        if (this.name && data[this.name]) {
            var propertyName = getPropertyName(this); // Get the property name
            var values = data[this.name][propertyName];

            if (Array.isArray(values)) {
                // Set the first value for simplicity
                if (values.length > 0) {
                    if (this.nodeName === 'INPUT' && this.type === 'checkbox') {
                        // For checkboxes, check the box if the value matches
                        this.checked = values.includes($(this).val());
                    } else {
                        $(this).val(values[0]); // Set the value to the first item in the array
                    }
                }
            } else {
                $(this).val(values); // Set the value directly if it's not an array
            }
        }
    });
}

function form_struc_2_json(FRM){
	
	var formElements = [];

	$('#' + FRM + ' :input').each(function() {
		var element = {
			type: this.tagName.toLowerCase(),
			id: $(this).attr('id'),
			name: $(this).attr('name'),
			class: $(this).attr('class'),
			value: $(this).val()
		};

		if (element.type === 'select') {
			element.options = [];
			$(this).find('option').each(function() {
				element.options.push({value: $(this).val(), text: $(this).text()});
			});
		}

		formElements.push(element);
	});

	var json = JSON.stringify(formElements);
	
	return json;
}
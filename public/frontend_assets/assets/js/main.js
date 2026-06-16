(function ($) {
  var $window = $(window),
    $body = $("body"),
    $doc = $(document);
  window.setLoading = (button) => {
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    button.setAttribute('disabled', true);
    button.disabled = true;
    return true;
  }
  window.unsetLoading = (button, TEXT) => {
    button.innerHTML = TEXT;
    button.disabled = false;
    button.removeAttribute('disabled');
    return true;
  }
  $window.on("scroll", function () {
    var scroll = $window.scrollTop();
    if (scroll < 30) {
      $(".sticky").removeClass("is-sticky");
    } else {
      $(".sticky").addClass("is-sticky");
    }
  });

  // Scroll to top active js
  $(window).on("scroll", function () {
    if ($(this).scrollTop() < 400) {
      $(".scroll-top").removeClass("visible");
    } else {
      $(".scroll-top").addClass("visible");
    }
  });
  $(".scroll-top").on("click", function (event) {
    $("html,body").animate(
      {
        scrollTop: 0,
      },
      1000
    );
  });
  $(".form-control").focus(function () {
    $(this).parent().addClass("focus");
  });
  $("select.form-control").focus(function () {
    $(this).parent().parent().parent(".select ").addClass("focus");
  });
  $(".form-control").focusout(function () {
    var a = $(this).val();
    if (a == "" || a == " ") {
      $(this).parent().removeClass("focus");
    }
  });
  $(".searchForm .input-text").focus(function () {
    $(this).parent().parent().addClass("focus");
  });
  $(".searchForm .input-text").focus(function () {
    $(this).parent().parent().parent().parent(".select ").addClass("focus");
  });
  $(".searchForm .input-text").focusout(function () {
    var a = $(this).val();
    if (a == "" || a == " ") {
      $(this).parent().parent().removeClass("focus");
    }
  });
  $("#showPassword").click(function () {
    var passwordField = $("#password");
    var passwordFieldType = passwordField.attr("type");

    $("img.normal").toggleClass("d-none");
    if (passwordFieldType === "password") {
      // Change the input type to "text" to show the password
      passwordField.attr("type", "text");
      $("img.hover").addClass("d-inline-block");
      $("img.hover").removeClass("d-none");
    } else {
      // Change the input type back to "password" to hide the password
      passwordField.attr("type", "password");
      $("img.hover").removeClass("d-inline-block");
      $("img.hover").addClass("d-none");
    }
  });
  $(".showPassword").click(function () {
    var passwordField = $(this).prev();
    var passwordFieldType = passwordField.attr("type");

    $("img.normal").toggleClass("d-none");
    if (passwordFieldType === "password") {
      // Change the input type to "text" to show the password
      passwordField.attr("type", "text");
      $("img.hover").addClass("d-inline-block");
      $("img.hover").removeClass("d-none");
    } else {
      // Change the input type back to "password" to hide the password
      passwordField.attr("type", "password");
      $("img.hover").removeClass("d-inline-block");
      $("img.hover").addClass("d-none");
    }
  });
  $(".showPasswordConfirm").click(function () {
    var passwordField = $(this).prev();
    var passwordFieldType = passwordField.attr("type");

    $("img.normalConfirm").toggleClass("d-none");
    if (passwordFieldType === "password") {
      // Change the input type to "text" to show the password
      passwordField.attr("type", "text");
      $("img.hoverConfirm").addClass("d-inline-block");
      $("img.hoverConfirm").removeClass("d-none");
    } else {
      // Change the input type back to "password" to hide the password
      passwordField.attr("type", "password");
      $("img.hoverConfirm").removeClass("d-inline-block");
      $("img.hoverConfirm").addClass("d-none");
    }
  });
  $(".showOldPassword").click(function () {
    var passwordField = $(this).prev();
    var passwordFieldType = passwordField.attr("type");

    $("img.normalOld").toggleClass("d-none");
    if (passwordFieldType === "password") {
      // Change the input type to "text" to show the password
      passwordField.attr("type", "text");
      $("img.hoverOld").addClass("d-inline-block");
      $("img.hoverOld").removeClass("d-none");
    } else {
      // Change the input type back to "password" to hide the password
      passwordField.attr("type", "password");
      $("img.hoverOld").removeClass("d-inline-block");
      $("img.hoverOld").addClass("d-none");
    }
  });
  $(".testimonial-slider").slick({
    speed: 1000,
    autoplay: true,
    slidesToShow: 4,
    // centerMode:true,
    dots: true,
    adaptiveHeight: true,
    arrows: true,
    prevArrow: `<button type="button" class="slick-prev"><svg width="12" height="12" viewBox="0 0 19 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M18.0464 31L1.99988 16L18.0464 1" stroke="black" stroke-width="2"/></svg></button>`,
    nextArrow: `<button type="button" class="slick-next"><svg width="12" height="12" viewBox="0 0 19 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L17.0465 16L1 31" stroke="black" stroke-width="2"/></svg></button>`,
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 2,
          arrows: false,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 1,
          arrows: false,
        },
      },
    ],
  });
})(jQuery);

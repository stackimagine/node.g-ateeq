if (typeof jQuery == "undefined") {
  if (typeof $ == "function") {
    // warning, global var
    thisPageUsingOtherJSLibrary = true;
  }

  function getScript(url, success) {
    var script = document.createElement("script");
    script.src = url;

    var head = document.getElementsByTagName("head")[0],
      done = false;

    // Attach handlers for all browsers
    script.onload = script.onreadystatechange = function () {
      if (
        !done &&
        (!this.readyState ||
          this.readyState == "loaded" ||
          this.readyState == "complete")
      ) {
        done = true;

        // callback function provided as param
        success();

        script.onload = script.onreadystatechange = null;
        head.removeChild(script);
      }
    };

    head.appendChild(script);
  }

  getScript("{/literal}{$file_path}{literal}", function () {
    if (typeof jQuery == "undefined") {
      // Super failsafe - still somehow failed...
    } else {
      if (thisPageUsingOtherJSLibrary) {
        // Run your jQuery Code
      } else {
        // Use .noConflict(), then run your jQuery Code
      }
    }
  });
}

$(document).ready(function () {
  // Initially hide the element with id "content"
  $("#content").hide();

  function isValidLicense() {
    $.ajax(
      "index.php?module=si_Email_Writer&action=outfitterscontroller&to_pdf=1",
      {
        type: "POST",
        dataType: "json",
        data: {
          method: "isValid",
        },
        success: function (response) {
          $("#content").show();

          if (!response) {
            window.location.href =
              "index.php?module=si_Email_Writer&action=license";
          }
        },
        error: function (jqXHR, textStatus, errorThrown) {
          window.location.href =
            "index.php?module=si_Email_Writer&action=license";
        },
      }
    );
  }

  // Call the isValidLicense function
  isValidLicense();
});

"use strict";

var spaddTrackButtons = document.querySelectorAll(".sp-add-track");
var spclearPlaylistButtons = document.querySelectorAll(".sp-clear-playlist");
var spTogglePlaylistButtons = document.querySelectorAll(".sp-toggle-playlist");
var spremoveTrackButtons = document.querySelectorAll(".sp-remove-track");
var spFormContainer = document.querySelector("#sp-track-container");
var mColorInput = document.querySelector("#sp-settings-mcolor");
var tColorInput = document.querySelector("#sp-settings-tcolor");
var aColorInput = document.querySelector("#sp-settings-acolor");
var sColorInput = document.querySelector("#sp-settings-scolor");
var pColorInput = document.querySelector("#sp-settings-pcolor"); // add new Fieldset DOM

spaddTrackButtons.forEach(function (button) {
  button.addEventListener("click", function (e) {
    e.preventDefault();
    var lastFieldSet = spFormContainer.lastElementChild;
    var lastKey;
    var nextKey;

    if (lastFieldSet) {
      lastKey = parseInt(lastFieldSet.dataset.key);
      nextKey = lastKey + 1;
    } else {
      nextKey = 1;
    }

    var newFieldset = "<fieldset data-key='"
      .concat(
        nextKey,
        '\'><div class="sp-toggle"> <h4></h4><span> &#9650;</span></div> <div class="sp-toggle-target"> <input type=\'text\' placeholder='
      )
      .concat(sp_scripts.newField.p_title, " name='sp-tracks[")
      .concat(
        nextKey,
        "][title]' class=\"sp-track-title\" required/> <input type='text' placeholder="
      )
      .concat(sp_scripts.newField.p_artiste, " name='sp-tracks[")
      .concat(
        nextKey,
        "][artiste]' class=\"sp-track-artiste\" required/> <div class='sp-input-music-upload-container'>  <input type='text' placeholder="
      )
      .concat(sp_scripts.newField.p_url, " name='sp-tracks[")
      .concat(
        nextKey,
        "][url]' class=\"sp-track-url\" required/> <button class=\"sp-upload\" >Upload</button>  </div><div class='sp-input-music-upload-container'> <input type='url' placeholder="
      )
      .concat(sp_scripts.newField.p_image, " name='sp-tracks[")
      .concat(
        nextKey,
        '][pic]\' class=\'sp-track-pic\'  /> <button class="sp-upload-pic" >Upload</button>  \n</div> <input type="button" class="sp-remove-track secondary" value="Remove Track"></div></fieldset>'
      );

    if (lastFieldSet) {
      lastFieldSet.insertAdjacentHTML("afterend", newFieldset);
    } else {
      spFormContainer.innerHTML = newFieldset;
    } // Re-register necessary DOM elements

    spRegisterRemoveButtons();
    registerTogglers();
    registerTitles();
    registerUploadButtons();
  });
});
/**
 * assign listener to 'Remove Track' buttons
 */

function spRegisterRemoveButtons() {
  spremoveTrackButtons = document.querySelectorAll(".sp-remove-track");
  spremoveTrackButtons.forEach(function (button) {
    button.addEventListener(
      "click",
      function (e) {
        e.preventDefault();
        button.parentElement.parentElement.remove();
      },
      {
        once: true,
      }
    );
  });
}
/**
 * assign listener to 'Title' fields
 */

function registerTitles() {
  var titleInputs = document.querySelectorAll(".sp-track-title");
  titleInputs.forEach(function (title) {
    title.addEventListener("input", function (e) {
      title.parentElement.parentElement
        .querySelector(".sp-toggle")
        .querySelector("h4").innerHTML = e.target.value;
    });
  });
}
/**
 * assign listener to 'Remove Track' buttons
 */

function registerTogglers() {
  var toggleBars = document.querySelectorAll(".sp-toggle");
  toggleBars.forEach(function (toggleBar) {
    var toggleBarText = toggleBar.querySelector("h4");
    var titleText = toggleBar.parentElement
      .querySelector(".sp-toggle-target")
      .querySelector(".sp-track-title");
    toggleBarText.innerHTML = titleText.value;
    toggleBar.addEventListener("click", function () {
      var target = toggleBar.parentElement.querySelector(".sp-toggle-target");
      var icon = toggleBar.querySelector("span");

      if (target.style.display == "") {
        target.style.display = "none";
        icon.style.transform = "rotate(180deg)";
      } else {
        target.style.display = "";
        icon.style.transform = "";
      }
    });
  });
  return toggleBars;
} // assign listener to "Toggle ALl" button

spTogglePlaylistButtons.forEach(function (button) {
  button.addEventListener("click", function (e) {
    e.preventDefault();
    var toggleBars = registerTogglers();
    toggleBars.forEach(function (toggleBar) {
      var target = toggleBar.parentElement.querySelector(".sp-toggle-target");
      var icon = toggleBar.querySelector("span");

      if (target.style.display == "") {
        target.style.display = "none";
        icon.style.transform = "rotate(180deg)";
      } else {
        target.style.display = "";
        icon.style.transform = "";
      }
    });
  });
}); // assign listener to 'Clear Playlist' button

spclearPlaylistButtons.forEach(function (button) {
  button.addEventListener("click", function (e) {
    e.preventDefault();
    var warning = confirm("".concat(sp_scripts.warning.clear));

    if (warning) {
      spFormContainer.innerHTML = "";
    } else {
      return;
    }
  });
}); // assign listener to 'Upload' button - WP upload modal

function registerUploadButtons() {
  jQuery(document).ready(function ($) {
    var spMediaUploader;
    $(".sp-upload").each(function () {
      var button = $(this);

      function imageUploader(e) {
        e.preventDefault(); // wp.media.frames.fle_frame

        spMediaUploader = wp.media({
          title: "Select a Song",
          library: {
            type: "audio",
          },
          button: {
            text: "Select Song",
          },
          multiple: false,
        });
        spMediaUploader.on("select", function () {
          var attachment = spMediaUploader
            .state()
            .get("selection")
            .first()
            .toJSON();
          button.parent().children(".sp-track-url").val(attachment.url);
          var title = button.parent().parent().children(".sp-track-title");
          var artiste = button.parent().parent().children(".sp-track-artiste");

          if (attachment.title && title.val() === "") {
            title.val(attachment.title);
            title
              .parent()
              .parent()
              .children(".sp-toggle")
              .children("h4")
              .html(attachment.title);
          }

          if (
            attachment.meta &&
            attachment.meta.artist &&
            artiste.val() === ""
          ) {
            artiste.val(attachment.meta.artist);
          }
        });
        spMediaUploader.open();
      }

      button.unbind("click");
      button.on("click", imageUploader);
    });
    $(".sp-upload-pic").each(function () {
      var button = $(this);
      button.unbind("click");
      button.on("click", function (e) {
        e.preventDefault(); // wp.media.frames.fle_frame

        spMediaUploader = wp.media({
          title: "Select a Cover Image",
          library: {
            type: "image",
          },
          button: {
            text: "Select Cover Image",
          },
          multiple: false,
        });
        spMediaUploader.on("select", function () {
          attachment = spMediaUploader
            .state()
            .get("selection")
            .first()
            .toJSON();
          console.log(attachment.url);
          button.parent().children(".sp-track-pic").val(attachment.url);
        });
        spMediaUploader.open();
      });
    });
  });
}

spRegisterRemoveButtons();
registerTogglers();
registerTitles();
registerUploadButtons(); // assign listener to 'Main Color' input field

mColorInput.addEventListener("change", function () {
  document.querySelector(".cp-container").style.background = mColorInput.value;
  document.querySelector("#cp-polygon").style.background = mColorInput.value;
}); // assign listener to 'Track Color' input field

tColorInput.addEventListener("change", function () {
  document.querySelectorAll(".cp-track").forEach(function (e) {
    return (e.style.background = tColorInput.value);
  });
}); // assign listener to 'Accent Color' input field

aColorInput.addEventListener("change", function () {
  document.querySelector(".cp-pause-duration").style.background =
    aColorInput.value;
  document.querySelector(".cp-track.cp-selected").style.color =
    aColorInput.value;
  document.querySelector(".cp-pause-play svg").style.fill = aColorInput.value;
  document.querySelector("#cp-play-options svg").style.fill = aColorInput.value;
}); // assign listener to 'Shadow Color' input field

sColorInput.addEventListener("change", function () {
  document.querySelector(".cp-pause-duration").style.boxShadow =
    "0px 0px 18px 0px " + sColorInput.value;
}); // assign listener to 'Primary Color' input field

pColorInput.addEventListener("change", function () {
  document.querySelectorAll(".cp-end svg").forEach(function (e) {
    return (e.style.fill = pColorInput.value);
  });
  document.querySelector(".cp-container").style.color = pColorInput.value;
});

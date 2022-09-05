const spaddTrackButtons = document.querySelectorAll(".sp-add-track");
const spclearPlaylistButtons = document.querySelectorAll(".sp-clear-playlist");
const spTogglePlaylistButtons = document.querySelectorAll(
  ".sp-toggle-playlist"
);
let spremoveTrackButtons = document.querySelectorAll(".sp-remove-track");
let spFormContainer = document.querySelector("#sp-track-container");
const mColorInput = document.querySelector("#sp-settings-mcolor");
const tColorInput = document.querySelector("#sp-settings-tcolor");
const aColorInput = document.querySelector("#sp-settings-acolor");
const sColorInput = document.querySelector("#sp-settings-scolor");
const pColorInput = document.querySelector("#sp-settings-pcolor");

// add new Fieldset DOM
spaddTrackButtons.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    const lastFieldSet = spFormContainer.lastElementChild;
    let lastKey;
    let nextKey;
    if (lastFieldSet) {
      lastKey = parseInt(lastFieldSet.dataset.key);
      nextKey = lastKey + 1;
    } else {
      nextKey = 1;
    }
    const newFieldset = `<fieldset data-key='${nextKey}'><div class="sp-toggle"> <h4></h4><span> &#9650;</span></div> <div class="sp-toggle-target"> <input type='text' placeholder=${sp_scripts.newField.p_title} name='sp-tracks[${nextKey}][title]' class="sp-track-title" required/> <input type='text' placeholder=${sp_scripts.newField.p_artiste} name='sp-tracks[${nextKey}][artiste]' class="sp-track-artiste" required/> <div class='sp-input-music-upload-container'>  <input type='text' placeholder=${sp_scripts.newField.p_url} name='sp-tracks[${nextKey}][url]' class="sp-track-url" required/> <button class="sp-upload" >Upload</button>  </div><div class='sp-input-music-upload-container'> <input type='url' placeholder=${sp_scripts.newField.p_image} name='sp-tracks[${nextKey}][pic]' class='sp-track-pic'  /> <button class="sp-upload-pic" >Upload</button>  
</div> <input type="button" class="sp-remove-track secondary" value="Remove Track"></div></fieldset>`;

    if (lastFieldSet) {
      lastFieldSet.insertAdjacentHTML("afterend", newFieldset);
    } else {
      spFormContainer.innerHTML = newFieldset;
    }
    // Re-register necessary DOM elements
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
  spremoveTrackButtons.forEach((button) => {
    button.addEventListener(
      "click",
      (e) => {
        e.preventDefault();
        button.parentElement.parentElement.remove();
      },
      { once: true }
    );
  });
}
/**
 * assign listener to 'Title' fields
 */
function registerTitles() {
  const titleInputs = document.querySelectorAll(".sp-track-title");
  titleInputs.forEach((title) => {
    title.addEventListener("input", (e) => {
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
  const toggleBars = document.querySelectorAll(".sp-toggle");
  toggleBars.forEach((toggleBar) => {
    const toggleBarText = toggleBar.querySelector("h4");
    const titleText = toggleBar.parentElement
      .querySelector(".sp-toggle-target")
      .querySelector(".sp-track-title");
    toggleBarText.innerHTML = titleText.value;

    toggleBar.addEventListener("click", () => {
      const target = toggleBar.parentElement.querySelector(".sp-toggle-target");
      const icon = toggleBar.querySelector("span");
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
}

// assign listener to "Toggle ALl" button
spTogglePlaylistButtons.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    const toggleBars = registerTogglers();
    toggleBars.forEach((toggleBar) => {
      const target = toggleBar.parentElement.querySelector(".sp-toggle-target");
      const icon = toggleBar.querySelector("span");
      if (target.style.display == "") {
        target.style.display = "none";
        icon.style.transform = "rotate(180deg)";
      } else {
        target.style.display = "";
        icon.style.transform = "";
      }
    });
  });
});

// assign listener to 'Clear Playlist' button
spclearPlaylistButtons.forEach((button) => {
  button.addEventListener("click", (e) => {
    e.preventDefault();
    let warning = confirm(`${sp_scripts.warning.clear}`);
    if (warning) {
      spFormContainer.innerHTML = "";
    } else {
      return;
    }
  });
});

// assign listener to 'Upload' button - WP upload modal
function registerUploadButtons() {
  jQuery(document).ready(function ($) {
    let spMediaUploader;
    $(".sp-upload").each(function () {
      let button = $(this);
      function imageUploader(e) {
        e.preventDefault();
        // wp.media.frames.fle_frame
        spMediaUploader = wp.media({
          title: "Select a Song",
          library: { type: "audio" },
          button: {
            text: "Select Song",
          },
          multiple: false,
        });

        spMediaUploader.on("select", function () {
          attachment = spMediaUploader
            .state()
            .get("selection")
            .first()
            .toJSON();

          button.parent().children(".sp-track-url").val(attachment.url);
          let title = button.parent().parent().children(".sp-track-title");
          let artiste = button.parent().parent().children(".sp-track-artiste");

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
      let button = $(this);
      button.unbind("click");
      button.on("click", function (e) {
        e.preventDefault();
        // wp.media.frames.fle_frame
        spMediaUploader = wp.media({
          title: "Select a Cover Image",
          library: { type: "image" },
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
registerUploadButtons();

// assign listener to 'Main Color' input field
mColorInput.addEventListener("change", () => {
  document.querySelector(".cp-container").style.background = mColorInput.value;
  document.querySelector("#cp-polygon").style.background = mColorInput.value;
});
// assign listener to 'Track Color' input field
tColorInput.addEventListener("change", () => {
  document
    .querySelectorAll(".cp-track")
    .forEach((e) => (e.style.background = tColorInput.value));
});
// assign listener to 'Accent Color' input field
aColorInput.addEventListener("change", () => {
  document.querySelector(".cp-pause-duration").style.background =
    aColorInput.value;
  document.querySelector(".cp-track.cp-selected").style.color =
    aColorInput.value;
  document.querySelector(".cp-pause-play svg").style.fill = aColorInput.value;
  document.querySelector("#cp-play-options svg").style.fill = aColorInput.value;
});
// assign listener to 'Shadow Color' input field
sColorInput.addEventListener("change", () => {
  document.querySelector(".cp-pause-duration").style.boxShadow =
    "0px 0px 18px 0px " + sColorInput.value;
});
// assign listener to 'Primary Color' input field
pColorInput.addEventListener("change", () => {
  document
    .querySelectorAll(".cp-end svg")
    .forEach((e) => (e.style.fill = pColorInput.value));
  document.querySelector(".cp-container").style.color = pColorInput.value;
});

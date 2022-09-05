"use strict";

var pluginAssetURL = "http://localhost/hypalong/wp-content/plugins/simple-playlist/public/music-player/";
var audio = document.querySelector("#cp-audio");
var replayOptions = document.querySelector("#cp-play-options");
var tracks = document.querySelectorAll(".cp-track-cont");
var replayState = "replay-all";
var currentTrackId;
var trackSrcArray = {};
var trackIds = [];
/**
 * Get music src
 * - Sets value of CurrentTrackId
 * - Returns url of next track
 *
 * @param {number} trackId - track index to load
 * @returns {string}
 */

function loadTrack(trackId) {
  currentTrackId = trackId;
  return trackSrcArray[trackId];
}
/**
 * Get HTML element that displays time
 * @returns {object}
 */


function getCurrentTimeDisplay() {
  var currTime = document.querySelector(".cp-selected").querySelector(".cp-load-play-animation").querySelector(".durTime");
  return currTime;
}
/**
 * Get HTML element with spinning loader for current track
 * @returns {object}
 */


function getCurrentTrackLoader() {
  var currentTrackLoader = document.querySelector(".cp-selected").querySelector(".cp-track-cont").querySelector(".cp-image-container").querySelector(".cp-loading");
  return currentTrackLoader;
}
/**
 * Get HTML elemnt with spinning loader
 * @returns {object}
 */


function getCurrentTrackDurationSlider() {
  var durSlider = document.querySelector(".cp-selected").querySelector(".cp-track-cont").querySelector(".cp-load-play-animation").querySelector(".cp-pause-duration-container").querySelector(".cp-pause-duration");
  return durSlider;
}
/**
 * Get HTML element for track seek background
 * @returns {object}
 */


function getCurTrackDurSliderContainer() {
  var durSliderContainer = document.querySelector(".cp-selected").querySelector(".cp-track-cont").querySelector(".cp-load-play-animation").querySelector(".cp-pause-duration-container");
  return durSliderContainer;
}
/**
 * Get Play/Pause svg HTML element
 * @returns {object}
 */


function getPausePlayButton() {
  var pausePlayButton = document.querySelector(".cp-selected").querySelector(".cp-load-play-animation").querySelector(".cp-pause-play");
  return pausePlayButton;
}

audio.addEventListener("timeupdate", DurTime);
audio.addEventListener("timeupdate", updateProgress);
audio.addEventListener("ended", effectReplayStatus);
audio.addEventListener("canplaythrough", function () {
  getCurrentTrackLoader().style.display = "";
}); // update replay state and icon

replayOptions.addEventListener("click", function (e) {
  switch (replayState) {
    case "replay-all":
      replayOptions.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" height=\"48\" width=\"48\">\n      <g ><path d=\"M5 46q-1.2 0-2.1-.9Q2 44.2 2 43V5q0-1.2.9-2.1Q3.8 2 5 2h38q1.2 0 2.1.9.9.9.9 2.1v38q0 1.2-.9 2.1-.9.9-2.1.9Zm9-2 2.1-2.2-4.3-4.3H38v-11h-3v8H11.8l4.3-4.3L14 28l-8 8Zm9.3-14.1h2.45V18H20.5v2.45h2.8ZM10 21.5h3v-8h23.2l-4.3 4.3L34 20l8-8-8-8-2.1 2.2 4.3 4.3H10Z\"/>\n      </g></svg>";
      replayState = "replay-once";
      break;

    case "replay-once":
      replayOptions.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" height=\"48\" width=\"48\">\n      <g >\n      <path d=\"M5 46q-1.2 0-2.1-.9Q2 44.2 2 43V5q0-1.2.9-2.1Q3.8 2 5 2h38q1.2 0 2.1.9.9.9.9 2.1v38q0 1.2-.9 2.1-.9.9-2.1.9Zm9-2 2.1-2.2-4.3-4.3H38v-11h-3v8H11.8l4.3-4.3L14 28l-8 8Zm-4-22.5h3v-8h23.2l-4.3 4.3L34 20l8-8-8-8-2.1 2.2 4.3 4.3H10Z\"/>\n      </g></svg>";
      replayOptions.querySelector("svg").classList.add("gray");
      replayState = "replay-none";
      break;

    case "replay-none":
      replayOptions.querySelector("svg").classList.remove("gray");
      replayState = "replay-all";
      break;

    default:
      break;
  }
});
/**
 * implement replay state
 */

function effectReplayStatus() {
  switch (replayState) {
    case "replay-all":
      var num = trackIds.indexOf(currentTrackId);
      var nextTrackId;
      var nextTrackSrc;

      if (num < trackIds.length - 1) {
        nextTrackId = trackIds[num + 1];
      } else {
        nextTrackId = trackIds[0];
      }

      document.querySelector(".cp-selected").classList.remove("cp-selected");
      var nextTrackContainer = document.querySelector("div[data-id='".concat(nextTrackId, "']"));
      nextTrackContainer.parentElement.classList.add("cp-selected");
      audio.src = loadTrack(nextTrackId);
      getCurrentTrackLoader().style.display = "flex";
      audio.play();
      break;

    case "replay-once":
      audio.src = loadTrack(currentTrackId);
      audio.play();
      break;

    case "replay-none":
      break;

    default:
      break;
  }
} // load track on click


tracks.forEach(function (track) {
  trackSrcArray[track.dataset.id] = track.dataset.url;
  trackIds.push(track.dataset.id);
  track.addEventListener("click", function (e) {
    // get outgoing track
    var outgoingTrack = document.querySelector(".cp-selected");

    if (outgoingTrack) {
      if (e.target !== getCurTrackDurSliderContainer()) {
        var playButton = getPausePlayButton();

        if (track.parentElement != outgoingTrack) {
          // remove 'cp-selected' from class list of outgoing track
          getCurrentTrackLoader().style.display = "";
          outgoingTrack.classList.remove("cp-selected");
          track.parentElement.classList.add("cp-selected"); // get incoming track's id and map to track array

          audio.src = loadTrack(track.dataset.id);
          getCurrentTrackLoader().style.display = "flex";
          audio.play();
        } else {
          if (audio.paused) {
            playButton.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" height=\"48\" width=\"48\"> <g > <path d=\"M26.25 38V10H38v28ZM10 38V10h11.75v28Z\"/> </g> </svg>";
            audio.play();
          } else {
            audio.pause();
            playButton.innerHTML = "<svg xmlns=\"http://www.w3.org/2000/svg\" height=\"48\" width=\"48\"><g ><path d=\"M16 37.85v-28l22 14Z\"/> </g> </svg>";
          }
        }
      } else if (e.target == getCurTrackDurSliderContainer()) {
        // audio seeking
        var durSliderCont = getCurTrackDurSliderContainer();
        var durSlider = durSliderCont.querySelector(".cp-pause-duration");
        console.log("slider pressed");
        var width = durSliderCont.clientWidth;
        var clickX = e.offsetX;
        var duration = audio.duration;
        audio.currentTime = clickX / width * duration;
      }
    } else {
      // get new track
      track.parentElement.classList.add("cp-selected");
      getCurrentTrackLoader().style.display = "flex";
      audio.src = loadTrack(track.dataset.id);
      audio.play();
    }
  });
});
/**
 * Update Time display HTML element
 */

function DurTime(e) {
  var currentTime = e.srcElement.currentTime;
  var sec;
  var sec_d; // define minutes currentTime

  var min = currentTime == null ? 0 : Math.floor(currentTime / 60);
  min = min < 10 ? "0" + min : min; // define seconds currentTime

  function get_sec(x) {
    if (Math.floor(x) >= 60) {
      for (var i = 1; i <= 60; i++) {
        if (Math.floor(x) >= 60 * i && Math.floor(x) < 60 * (i + 1)) {
          sec = Math.floor(x) - 60 * i;
          sec = sec < 10 ? "0" + sec : sec;
        }
      }
    } else {
      sec = Math.floor(x);
      sec = sec < 10 ? "0" + sec : sec;
    }
  }

  get_sec(currentTime, sec); // change currentTime DOM

  var currTime = getCurrentTimeDisplay();
  currTime.innerHTML = min + ":" + sec;
}
/**
 * Update track duration while playing
 */


function updateProgress(e) {
  var _e$srcElement = e.srcElement,
      duration = _e$srcElement.duration,
      currentTime = _e$srcElement.currentTime;
  var progressPercent = currentTime / duration * 100;
  var progress = getCurrentTrackDurationSlider();
  progress.style.width = "".concat(progressPercent, "%");
}

const pluginAssetURL =
  "http://localhost/hypalong/wp-content/plugins/simple-playlist/public/music-player/";
const audio = document.querySelector("#cp-audio");
const replayOptions = document.querySelector("#cp-play-options");
const tracks = document.querySelectorAll(".cp-track-cont");
let replayState = "replay-all";
let currentTrackId;
let trackSrcArray = {};
let trackIds = [];
function loadTrack(trackId) {
  currentTrackId = trackId;
  return trackSrcArray[trackId];
}
function getCurrentTimeDisplay() {
  const currTime = document
    .querySelector(".cp-selected")
    .querySelector(".cp-load-play-animation")
    .querySelector(".durTime");
  return currTime;
}
function getCurrentTrackDurationSlider() {
  const durSlider = document
    .querySelector(".cp-selected")
    .querySelector(".cp-track-cont")
    .querySelector(".cp-load-play-animation")
    .querySelector(".cp-pause-duration-container")
    .querySelector(".cp-pause-duration");
  return durSlider;
}
function getCurTrackDurSliderContainer() {
  const durSliderContainer = document
    .querySelector(".cp-selected")
    .querySelector(".cp-track-cont")
    .querySelector(".cp-load-play-animation")
    .querySelector(".cp-pause-duration-container");
  return durSliderContainer;
}
function getPausePlayButton() {
  const pausePlayButton = document
    .querySelector(".cp-selected")
    .querySelector(".cp-load-play-animation")
    .querySelector(".cp-pause-play");
  return pausePlayButton;
}
// Time of song
audio.addEventListener("timeupdate", DurTime);
audio.addEventListener("timeupdate", updateProgress);
audio.addEventListener("ended", effectReplayStatus);
replayOptions.addEventListener("click", (e) => {
  switch (replayState) {
    case "replay-all":
      replayOptions.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
      <g ><path d="M5 46q-1.2 0-2.1-.9Q2 44.2 2 43V5q0-1.2.9-2.1Q3.8 2 5 2h38q1.2 0 2.1.9.9.9.9 2.1v38q0 1.2-.9 2.1-.9.9-2.1.9Zm9-2 2.1-2.2-4.3-4.3H38v-11h-3v8H11.8l4.3-4.3L14 28l-8 8Zm9.3-14.1h2.45V18H20.5v2.45h2.8ZM10 21.5h3v-8h23.2l-4.3 4.3L34 20l8-8-8-8-2.1 2.2 4.3 4.3H10Z"/>
      </g></svg>`;
      replayState = "replay-once";
      break;
    case "replay-once":
      replayOptions.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" height="48" width="48">
      <g >
      <path d="M5 46q-1.2 0-2.1-.9Q2 44.2 2 43V5q0-1.2.9-2.1Q3.8 2 5 2h38q1.2 0 2.1.9.9.9.9 2.1v38q0 1.2-.9 2.1-.9.9-2.1.9Zm9-2 2.1-2.2-4.3-4.3H38v-11h-3v8H11.8l4.3-4.3L14 28l-8 8Zm-4-22.5h3v-8h23.2l-4.3 4.3L34 20l8-8-8-8-2.1 2.2 4.3 4.3H10Z"/>
      </g></svg>`;
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
function effectReplayStatus() {
  switch (replayState) {
    case "replay-all":
      let num = trackIds.indexOf(currentTrackId);
      let nextTrackId;
      let nextTrackSrc;
      if (num < trackIds.length - 1) {
        nextTrackId = trackIds[num + 1];
      } else {
        nextTrackId = trackIds[0];
      }
      document.querySelector(`.cp-selected`).classList.remove("cp-selected");
      const nextTrackContainer = document.querySelector(
        `div[data-id='${nextTrackId}']`
      );
      nextTrackContainer.parentElement.classList.add("cp-selected");
      audio.src = loadTrack(nextTrackId);
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
}
tracks.forEach((track) => {
  trackSrcArray[track.dataset.id] = track.dataset.url;
  trackIds.push(track.dataset.id);
  // console.dir(trackSrcArray);
  // trackSrcArray.push(track.dataset.url);
  // trackSrcArray.sort();
  track.addEventListener("click", (e) => {
    //get outgoing track
    const outgoingTrack = document.querySelector(".cp-selected");
    if (outgoingTrack) {
      if (e.target !== getCurTrackDurSliderContainer()) {
        const playButton = getPausePlayButton();
        if (track.parentElement != outgoingTrack) {
          //remove 'cp-selected' from class list of outgoing track
          outgoingTrack.classList.remove("cp-selected");
          track.parentElement.classList.add("cp-selected");
          //get incoming track's id and map to track array

          audio.src = loadTrack(track.dataset.id);
          audio.play();
        } else {
          if (audio.paused) {
            playButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" height="48" width="48"> <g > <path d="M26.25 38V10H38v28ZM10 38V10h11.75v28Z"/> </g> </svg>`;
            audio.play();
          } else {
            audio.pause();
            playButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" height="48" width="48"><g ><path d="M16 37.85v-28l22 14Z"/> </g> </svg>`;
          }
        }
      } else if (e.target == getCurTrackDurSliderContainer()) {
        const durSliderCont = getCurTrackDurSliderContainer();
        const durSlider = durSliderCont.querySelector(".cp-pause-duration");
        console.log("slider pressed");
        const width = durSliderCont.clientWidth;
        const clickX = e.offsetX;
        const duration = audio.duration;
        audio.currentTime = (clickX / width) * duration;
      }
    } else {
      track.parentElement.classList.add("cp-selected");
      //get incoming track's id and map to track array
      audio.src = loadTrack(track.dataset.id);
      audio.play();
    }
  });

  function timekeeping() {}
});

function DurTime(e) {
  const { currentTime } = e.srcElement;
  var sec;
  var sec_d;

  // define minutes currentTime
  let min = currentTime == null ? 0 : Math.floor(currentTime / 60);
  min = min < 10 ? "0" + min : min;

  // define seconds currentTime
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

  get_sec(currentTime, sec);

  // change currentTime DOM
  let currTime = getCurrentTimeDisplay();
  currTime.innerHTML = min + ":" + sec;
}
function updateProgress(e) {
  const { duration, currentTime } = e.srcElement;
  const progressPercent = (currentTime / duration) * 100;
  const progress = getCurrentTrackDurationSlider();
  progress.style.width = `${progressPercent}%`;
}

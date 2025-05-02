import {timeline,timelineMetadata} from "./Timeline";
import {timelineChild,timelineChildMetadata} from "./Timeline-item";

const { registerModule } = window.divi.moduleLibrary;

const {
  addAction,
} = window?.vendor?.wp?.hooks;


addAction('divi.moduleLibrary.registerModuleLibraryStore.after', 'tmdivi', () => {
  registerModule(timelineMetadata, timeline);
  registerModule(timelineChildMetadata, timelineChild);
});

// document.addEventListener("DOMContentLoaded", function () {
//   registerModule(timelineMetadata, timeline);
//   registerModule(timelineChildMetadata, timelineChild);
// });